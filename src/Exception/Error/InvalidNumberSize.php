<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

final class InvalidNumberSize extends InvalidSize
{

    public function __construct(string $path, ?float $minimal, ?float $maximal, bool $withMinimal, bool $withMaximal)
    {
        parent::__construct($path, $minimal, $maximal, $withMinimal, $withMaximal);
    }

    protected function createMessage(?float $from, ?float $to, bool $withFrom, bool $withTo): string
    {
        $size = [];
        if (!is_null($from)) {
            $size[0] = ' >';
            if ($withFrom) {
                $size[0] .= '=';
            }
            $size[0] .= ' ' . $from;
        }
        if (!is_null($to)) {
            $size[1] = ' <';
            if ($withTo) {
                $size[1] .= '=';
            }
            $size[1] .= ' ' . $to;
        }
        return 'value must be ' . implode(' and ', $size);
    }

    public function getToken(): string
    {
        return self::TOKEN_VALUE_OUT_OF_LIMITS;
    }
}
