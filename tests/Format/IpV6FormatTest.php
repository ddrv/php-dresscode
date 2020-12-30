<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Format;

use Ddrv\DressCode\Format\Format;
use Ddrv\DressCode\Format\IpFormat;

final class IpV6FormatTest extends FormatTestCase
{

    protected function getFormat(): Format
    {
        return IpFormat::ipv6();
    }

    public function provideCorrectValues(): array
    {
        return [
            ['2001:0DB8:3C4D:7777:0260:3EFF:FE15:9501'],
            ['2001:0db8:3c4d:7777:0260:3eff:fe15:9501'],
            ['2001:0db8:3c4d::fe15:9501'],
            ['0000::0000'],
            ['FFFF::FFFF'],
        ];
    }

    public function provideIncorrectValues(): array
    {
        return [
            ['0.0.0.0'],
            ['127.0.0.1'],
            ['192.168.1.1'],
            ['255.255.255.255'],
            ['256.0.0.1'],
            ['127..1'],
            ['192.168.001.001'],
            ['ip'],
            ['2001::3c4d::fe15:9501'],
            ['[2001:0db8:3c4d:7777:0260:3eff:fe15:9501]'],
        ];
    }
}
