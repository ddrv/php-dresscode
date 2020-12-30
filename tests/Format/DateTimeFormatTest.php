<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Format;

use Ddrv\DressCode\Format\DateTimeFormat;
use Ddrv\DressCode\Format\Format;

final class DateTimeFormatTest extends FormatTestCase
{

    protected function getFormat(): Format
    {
        return new DateTimeFormat();
    }

    public function provideCorrectValues(): array
    {
        return [
            ['2020-01-31T00:00:00Z'],
            ['1996-02-29T23:59:59UTC'],
            ['1923-06-14T01:03:22GMT'],
            ['1986-03-15T01:03:22+07:00'],
            ['2020-01-31T00:00:00.324Z'],
            ['1996-02-29T23:59:59.234UTC'],
            ['1923-06-14T01:03:22.856GMT'],
            ['1986-03-15T01:03:22.536+07:00'],
            ['2020-01-31T00:00:00.3245Z'],
        ];
    }

    public function provideIncorrectValues(): array
    {
        return [
            ['2020-01-32'],
            ['1997-02-29'],
            ['no date'],
            ['2020-01-31T00:00:00'],
            ['1996-02-29T24:59:59UTC'],
            ['1923-06-14T23:60:22GMT'],
            ['1986-03-15T01:03:60+07:00'],
            ['2020-01-31T00:00:00.324'],
        ];
    }
}
