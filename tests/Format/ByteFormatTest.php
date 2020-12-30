<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Format;

use Ddrv\DressCode\Format\ByteFormat;
use Ddrv\DressCode\Format\Format;

final class ByteFormatTest extends FormatTestCase
{

    protected function getFormat(): Format
    {
        return new ByteFormat();
    }

    public function provideCorrectValues(): array
    {
        return [
            [base64_encode('value_one')],
            [base64_encode('value_two')],
            [base64_encode('value_three')],
        ];
    }

    public function provideIncorrectValues(): array
    {
        return [[chr(1)], [chr(2)], [chr(3)]];
    }
}
