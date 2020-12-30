<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Format;

use Ddrv\DressCode\Format\Format;
use Ddrv\DressCode\Format\HostnameFormat;

final class HostnameFormatTest extends FormatTestCase
{

    protected function getFormat(): Format
    {
        return new HostnameFormat();
    }

    public function provideCorrectValues(): array
    {
        return [
            ['localhost'],
            ['сайт.рф'],
            ['site.com'],
            ['127.0.0.1'],
            ['super-site.com'],
        ];
    }

    public function provideIncorrectValues(): array
    {
        return [
            ['site.com:8080'],
            ['192.168.1.1:3045'],
            ['super_site.com'],
            ['-supersite.com'],
        ];
    }
}
