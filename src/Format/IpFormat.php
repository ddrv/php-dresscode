<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Format;

use Ddrv\DressCode\Exception\WrongFormatException;

final class IpFormat extends Format
{

    /**
     * @var bool
     */
    private $v4;

    /**
     * @var bool
     */
    private $v6;

    /**
     * @var string
     */
    private $format;

    private function __construct(bool $v4, bool $v6, string $format)
    {
        $this->v4 = $v4;
        $this->v6 = $v6;
        $this->format = $format;
    }

    public static function all(): self
    {
        return new self(true, true, 'ip');
    }

    public static function ipv4(): self
    {
        return new self(true, false, 'ipv4');
    }

    public static function ipv6(): self
    {
        return new self(false, true, 'ipv6');
    }

    /**
     * @inheritDoc
     */
    public function check(string $value): void
    {
        $flags = 0;
        if ($this->v4) {
            $flags |= FILTER_FLAG_IPV4;
        }
        if ($this->v6) {
            $flags |= FILTER_FLAG_IPV6;
        }
        if (filter_var($value, FILTER_VALIDATE_IP, $flags) === false) {
            throw new WrongFormatException($this->format);
        }
    }
}
