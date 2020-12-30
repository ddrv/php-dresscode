<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Format;

use Ddrv\DressCode\Exception\WrongFormatException;

final class EmailFormat extends Format
{

    /**
     * @inheritDoc
     */
    public function check(string $value): void
    {
        $arr = explode('@', $value);
        $badUser = !$arr[0] || strpos($arr[0], ' ') !== false;
        $badHost = !$arr[1] || strpos($arr[1], ' ') !== false;
        if (count($arr) !== 2  || $badUser || $badHost) {
            throw new WrongFormatException('email');
        }
    }
}
