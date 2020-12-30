<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Format;

use Ddrv\DressCode\Exception\WrongFormatException;

abstract class Format
{

    /**
     * @param string $value
     * @throws WrongFormatException
     */
    abstract public function check(string $value): void;
}
