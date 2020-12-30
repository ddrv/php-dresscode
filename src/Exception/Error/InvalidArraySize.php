<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

final class InvalidArraySize extends InvalidSize
{

    public function __construct(string $path, ?int $minimal, ?int $maximal)
    {
        parent::__construct($path, $minimal, $maximal, false, false);
    }

    protected function createMessage(?float $from, ?float $to, bool $withFrom, bool $withTo): string
    {
        $size = '';
        if (!$from && !$to) {
            return 'error array size';
        }
        if ($from && !$to) {
            $size = 'at least ' . $from;
        }
        if (!$from && $to) {
            $size = 'up to ' . $to;
        }
        if ($from && $to) {
            $size = 'between ' . $from . ' to ' . $to;
        }
        return 'array size must be ' . $size . ' items';
    }

    public function getToken(): string
    {
        return self::TOKEN_INVALID_ARRAY_SIZE;
    }
}
