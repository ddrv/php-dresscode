<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Format;

final class VoidFormat extends Format
{

    /**
     * @inheritDoc
     */
    public function check(string $value): void
    {
    }
}
