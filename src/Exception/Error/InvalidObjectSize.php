<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

final class InvalidObjectSize extends InvalidSize
{

    public function __construct(string $path, ?int $minimal, ?int $maximal)
    {
        parent::__construct($path, $minimal, $maximal, true, true);
    }

    protected function createMessage(?float $from, ?float $to, bool $withFrom, bool $withTo): string
    {
        $size = '';
        if (!$from && !$to) {
            return 'error object size';
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
        return 'object must be contains ' . $size . ' properties';
    }

    public function getToken(): string
    {
        return self::TOKEN_INVALID_OBJECT_SIZE;
    }
}
