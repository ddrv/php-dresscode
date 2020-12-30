<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Format;

use Ddrv\DressCode\Exception\WrongFormatException;
use Ddrv\DressCode\Format\Format;
use PHPUnit\Framework\TestCase;

abstract class FormatTestCase extends TestCase
{

    /**
     * @param string $value
     * @dataProvider provideCorrectValues
     * @throws WrongFormatException
     */
    public function testCorrectValue(string $value)
    {
        $format = $this->getFormat();
        $format->check($value);
        $this->assertTrue(true);
    }

    /**
     * @param string $value
     * @dataProvider provideIncorrectValues
     */
    public function testIncorrectValue(string $value)
    {
        $format = $this->getFormat();
        $this->expectException(WrongFormatException::class);
        $format->check($value);
    }

    abstract protected function getFormat(): Format;

    abstract public function provideCorrectValues(): array;

    abstract public function provideIncorrectValues(): array;
}
