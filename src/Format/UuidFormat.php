<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Format;

use Ddrv\DressCode\Exception\WrongFormatException;

final class UuidFormat extends Format
{

    /**
     * @inheritDoc
     */
    public function check(string $value): void
    {
        $pattern = '/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}/u';
        if (!preg_match($pattern, $value)) {
            throw new WrongFormatException('uuid');
        }
    }
}
