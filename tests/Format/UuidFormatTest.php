<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Format;

use Ddrv\DressCode\Format\Format;
use Ddrv\DressCode\Format\UuidFormat;

final class UuidFormatTest extends FormatTestCase
{

    protected function getFormat(): Format
    {
        return new UuidFormat();
    }

    public function provideCorrectValues(): array
    {
        return [
            ['4896c91b-9e61-3129-87b6-8aa299028058'],
            ['29be0ee3-fe77-331e-a1bf-9494ec18c0ba'],
            ['33B06619-1EE7-3DB5-827D-0DC85DF1F759'],
            ['DB31F755-8F10-6619-7D29-33BDC85D0EE7'],
        ];
    }

    public function provideIncorrectValues(): array
    {
        return [
            ['DB31F7558F1066197D2933BDC85D0EE7'],
            ['DB31F755 8F10 6619 7D29 33BDC85D0EE7'],
            ['DB31F755_8F10_6619_7D29_33BDC85D0EE7'],
            ['DB31-F755-8F106619-7D29-33BDC85D0EE7'],
        ];
    }
}
