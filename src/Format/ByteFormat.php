<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Format;

use Ddrv\DressCode\Exception\WrongFormatException;

final class ByteFormat extends Format
{

    /**
     * @inheritDoc
     */
    public function check(string $value): void
    {
        if (!preg_match('/^[a-zA-Z0-9+\/]+(={1,3})?$/u', $value) && !preg_match('/^[a-zA-Z0-9*\-]+?$/u', $value)) {
            throw new WrongFormatException('byte');
        }
    }
}
