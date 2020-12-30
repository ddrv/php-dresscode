<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Format;

use DateTime;
use Ddrv\DressCode\Exception\WrongFormatException;
use Throwable;

final class DateFormat extends Format
{

    /**
     * @inheritDoc
     */
    public function check(string $value): void
    {
        $date = DateTime::createFromFormat('Y-m-d', $value);
        if ($date && $date->format('Y-m-d') === $value) {
            return;
        }
        throw new WrongFormatException('date');
    }
}
