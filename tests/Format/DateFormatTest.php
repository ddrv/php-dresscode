<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Format;

use Ddrv\DressCode\Format\DateFormat;
use Ddrv\DressCode\Format\Format;

final class DateFormatTest extends FormatTestCase
{

    protected function getFormat(): Format
    {
        return new DateFormat();
    }

    public function provideCorrectValues(): array
    {
        return [
            ['2020-01-31'],
            ['1996-02-29'],
            ['1986-03-15'],
        ];
    }

    public function provideIncorrectValues(): array
    {
        return [
            ['2020-01-32'],
            ['1997-02-29'],
            ['no date'],
        ];
    }
}
