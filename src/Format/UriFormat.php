<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Format;

use Ddrv\DressCode\Exception\WrongFormatException;

final class UriFormat extends Format
{

    /**
     * @inheritDoc
     */
    public function check(string $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new WrongFormatException('uri');
        }
    }
}
