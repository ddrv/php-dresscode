<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Tool;

/**
 * copied from https://github.com/true/php-punycode
 */
final class PunycodeEncoder
{
    private const BASE         = 36;
    private const T_MIN        = 1;
    private const T_MAX        = 26;
    private const SKEW         = 38;
    private const DAMP         = 700;
    private const INITIAL_BIAS = 72;
    private const INITIAL_N    = 128;
    private const PREFIX       = 'xn--';
    private const DELIMITER    = '-';

    private static $encodeTable = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l',
        'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
        'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
    );

    public function encode(string $input): ?string
    {
        $input = mb_strtolower($input);
        $parts = explode('.', $input);
        foreach ($parts as &$part) {
            $length = strlen($part);
            if ($length < 1) {
                return null;
            }
            $part = $this->encodePart($part);
            if (is_null($part)) {
                return null;
            }
        }
        $output = implode('.', $parts);
        $length = strlen($output);
        if ($length > 255) {
            return null;
        }

        return $output;
    }

    private function encodePart($input): ?string
    {
        $codePoints = $this->listCodePoints($input);

        $n = self::INITIAL_N;
        $bias = self::INITIAL_BIAS;
        $delta = 0;
        $h = $b = count($codePoints['basic']);

        $output = '';
        foreach ($codePoints['basic'] as $code) {
            $output .= $this->codePointToChar($code);
        }
        if ($input === $output) {
            return $output;
        }
        if ($b > 0) {
            $output .= self::DELIMITER;
        }

        $codePoints['nonBasic'] = array_unique($codePoints['nonBasic']);
        sort($codePoints['nonBasic']);

        $i = 0;
        $length = mb_strlen($input);
        while ($h < $length) {
            $m = $codePoints['nonBasic'][$i++];
            $delta = $delta + ($m - $n) * ($h + 1);
            $n = $m;

            foreach ($codePoints['all'] as $c) {
                if ($c < $n || $c < self::INITIAL_N) {
                    $delta++;
                }
                if ($c === $n) {
                    $q = $delta;
                    for ($k = self::BASE;; $k += self::BASE) {
                        $t = $this->calculateThreshold($k, $bias);
                        if ($q < $t) {
                            break;
                        }

                        $code = $t + (($q - $t) % (self::BASE - $t));
                        $output .= self::$encodeTable[$code];

                        $q = ($q - $t) / (self::BASE - $t);
                    }

                    $output .= self::$encodeTable[$q];
                    $bias = $this->adapt($delta, $h + 1, ($h === $b));
                    $delta = 0;
                    $h++;
                }
            }

            $delta++;
            $n++;
        }
        $out = self::PREFIX . $output;
        $length = strlen($out);
        if ($length > 63 || $length < 1) {
            return null;
        }

        return $out;
    }

    private function listCodePoints($input): array
    {
        $codePoints = [
            'all'      => [],
            'basic'    => [],
            'nonBasic' => [],
        ];

        $length = mb_strlen($input);
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($input, $i, 1);
            $code = $this->charToCodePoint($char);
            if ($code < 128) {
                $codePoints['all'][] = $codePoints['basic'][] = $code;
            } else {
                $codePoints['all'][] = $codePoints['nonBasic'][] = $code;
            }
        }

        return $codePoints;
    }

    private function codePointToChar($code): string
    {
        if ($code <= 0x7F) {
            return chr($code);
        } elseif ($code <= 0x7FF) {
            return chr(($code >> 6) + 192) . chr(($code & 63) + 128);
        } elseif ($code <= 0xFFFF) {
            return chr(($code >> 12) + 224) . chr((($code >> 6) & 63) + 128) . chr(($code & 63) + 128);
        } else {
            return chr(($code >> 18) + 240)
                . chr((($code >> 12) & 63) + 128)
                . chr((($code >> 6) & 63) + 128)
                . chr(($code & 63) + 128)
                ;
        }
    }

    private function charToCodePoint($char)
    {
        $code = ord($char[0]);
        if ($code < 128) {
            return $code;
        } elseif ($code < 224) {
            return (($code - 192) * 64) + (ord($char[1]) - 128);
        } elseif ($code < 240) {
            return (($code - 224) * 4096) + ((ord($char[1]) - 128) * 64) + (ord($char[2]) - 128);
        } else {
            return (($code - 240) * 262144)
                + ((ord($char[1]) - 128) * 4096)
                + ((ord($char[2]) - 128) * 64)
                + (ord($char[3]) - 128)
                ;
        }
    }

    private function calculateThreshold($k, $bias): int
    {
        if ($k <= $bias + self::T_MIN) {
            return self::T_MIN;
        } elseif ($k >= $bias + self::T_MAX) {
            return self::T_MAX;
        }
        return $k - $bias;
    }

    private function adapt($delta, $numPoints, $firstTime): int
    {
        $delta = (int) (
        ($firstTime)
            ? $delta / self::DAMP
            : $delta / 2
        );
        $delta += (int) ($delta / $numPoints);

        $k = 0;
        while ($delta > ((self::BASE - self::T_MIN) * self::T_MAX) / 2) {
            $delta = (int) ($delta / (self::BASE - self::T_MIN));
            $k = $k + self::BASE;
        }
        $k = $k + (int) (((self::BASE - self::T_MIN + 1) * $delta) / ($delta + self::SKEW));

        return $k;
    }
}
