<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Format;

use Ddrv\DressCode\Format\Format;
use Ddrv\DressCode\Format\UriFormat;

final class UriFormatTest extends FormatTestCase
{

    protected function getFormat(): Format
    {
        return new UriFormat();
    }

    public function provideCorrectValues(): array
    {
        return [
            ['http://localhost'],
            ['https://localhost'],
            ['tcp://localhost'],
            ['ftp://localhost'],
            ['ftp://user@password@localhost'],
            ['tcp://localhost:22'],
            ['http://localhost:22/path/to/page'],
            ['http://localhost:22/path/to/page?query=1'],
            ['http://site.com:22/path/to/page?query1=1&query2=2&arr[]=1&arr[]=2#fragment'],
        ];
    }

    public function provideIncorrectValues(): array
    {
        return [
            ['localhost'],
            ['site.com'],
            ['/path/to/page'],
        ];
    }
}
