<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Format;

use Ddrv\DressCode\Format\Format;
use Ddrv\DressCode\Format\PatternFormat;
use Ddrv\DressCode\Format\VoidFormat;

final class PatternFormatTest extends FormatTestCase
{

    protected function getFormat(): Format
    {
        return new PatternFormat(new VoidFormat(), '^[a-z_]+$');
    }

    public function provideCorrectValues(): array
    {
        return [['value_one'], ['value_two'], ['value_three']];
    }

    public function provideIncorrectValues(): array
    {
        return [['value-1'], ['value2'], ['value three']];
    }
}
