<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Format;

use Ddrv\DressCode\Format\EmailFormat;
use Ddrv\DressCode\Format\Format;

final class EmailFormatTest extends FormatTestCase
{

    protected function getFormat(): Format
    {
        return new EmailFormat();
    }

    public function provideCorrectValues(): array
    {
        return [
            ['user@localhost'],
            ['user@site.com'],
            ['vasily@сайт.рф'],
            ['mr.user@site.com'],
            ['user+tag@site.com'],
        ];
    }

    public function provideIncorrectValues(): array
    {
        return [
            ['@'],
            ['user@'],
            ['@user'],
            ['user @ host'],
        ];
    }
}
