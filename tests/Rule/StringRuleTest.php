<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidEnumValue;
use Ddrv\DressCode\Exception\Error\InvalidFormat;
use Ddrv\DressCode\Exception\Error\InvalidStringSize;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Format\VoidFormat;
use Ddrv\DressCode\Rule;
use Ddrv\DressCode\Rule\StringRule;
use PHPUnit\Framework\TestCase;

final class StringRuleTest extends TestCase
{

    /**
     * @param mixed $value
     * @param int|null $minLength
     * @param int|null $maxLength
     * @param array|null $enum
     * @throws InvalidValueException
     * @dataProvider provideCorrectValues
     */
    public function testCorrectValue($value, ?int $minLength, ?int $maxLength, ?array $enum)
    {
        $this->checkCorrect($value, $minLength, $maxLength, $enum, false);
    }

    /**
     * @param mixed $value
     * @param int|null $minLength
     * @param int|null $maxLength
     * @param array|null $enum
     * @dataProvider provideIncorrectValues
     */
    public function testIncorrectValue($value, ?int $minLength, ?int $maxLength, ?array $enum)
    {
        $this->checkIncorrectValue($value, $minLength, $maxLength, $enum, false);
    }

    /**
     * @param mixed $value
     * @param int|null $minLength
     * @param int|null $maxLength
     * @param array|null $enum
     * @throws InvalidValueException
     * @dataProvider provideStrictCorrectValues
     */
    public function testStrictCorrectValue($value, ?int $minLength, ?int $maxLength, ?array $enum)
    {
        $this->checkCorrect($value, $minLength, $maxLength, $enum, true);
    }

    /**
     * @param mixed $value
     * @param int|null $minLength
     * @param int|null $maxLength
     * @param array|null $enum
     * @dataProvider provideStrictIncorrectValues
     */
    public function testStrictIncorrectValue($value, ?int $minLength, ?int $maxLength, ?array $enum)
    {
        $this->checkIncorrectValue($value, $minLength, $maxLength, $enum, true);
    }

    public function provideCorrectValues(): array
    {
        $string = $this->getStringObject();
        return [
            ['value1',     null, null, ['value1', 'value2', 'value3']],
            ['value2',     null, null, ['value1', 'value2', 'value3']],
            ['value3',     null, null, ['value1', 'value2', 'value3']],
            [$string,      null, null, null],
            ['value',      null, 5,    null],
            ['val',        null, 5,    null],
            ['',           null, 5,    null],
            ['value',      5,    null, null],
            ['value1',     5,    null, null],
            ['value',      5,    5,    null],
            ['value',      5,    10,   null],
            ['value3',     5,    10,   null],
            ['value10000', 5,    10,   null],
        ];
    }

    public function provideIncorrectValues(): array
    {
        return [
            ['',       null, null, ['value1', 'value2', 'value3']],
            ['value',  null, null, ['value1', 'value2', 'value3']],
            ['values', null, 5,    null],
            ['test',   5,    null, null],
            ['test',   5,    10,   null],
            [100,      5,    10,   null],
            ['value1', 3,    5,   null],
        ];
    }

    public function provideStrictCorrectValues(): array
    {
        $string = $this->getStringObject();
        return [
            ['value1',     null, null, ['value1', 'value2', 'value3']],
            ['value2',     null, null, ['value1', 'value2', 'value3']],
            ['value3',     null, null, ['value1', 'value2', 'value3']],
            [$string,      null, null, null],
            ['value',      null, 5,    null],
            ['val',        null, 5,    null],
            ['',           null, 5,    null],
            ['value',      5,    null, null],
            ['value1',     5,    null, null],
            ['value',      5,    5,    null],
            ['value',      5,    10,   null],
            ['value3',     5,    10,   null],
            ['value10000', 5,    10,   null],
        ];
    }

    public function provideStrictIncorrectValues(): array
    {
        return [
            ['',       null, null, ['value1', 'value2', 'value3']],
            ['value',  null, null, ['value1', 'value2', 'value3']],
            ['values', null, 5,    null],
            ['test',   5,    null, null],
            ['test',   5,    10,   null],
            [100,      5,    10,   null],
            ['value1', 3,    5,    null],
            [10000000, 5,    10,   null],
            [true,     5,    10,   null],
        ];
    }

    /**
     * @param mixed $value
     * @param int|null $minLength
     * @param int|null $maxLength
     * @param array|null $enum
     * @param bool $strictTypes
     * @throws InvalidValueException
     */
    private function checkCorrect($value, ?int $minLength, ?int $maxLength, ?array $enum, bool $strictTypes)
    {
        $rule = $this->getRule($minLength, $maxLength, $enum);
        $result = $rule->validate(Action::input($strictTypes), '', $value);
        $this->assertSame((string)$value, $result[0]);
    }

    /**
     * @param mixed $value
     * @param int|null $minLength
     * @param int|null $maxLength
     * @param array|null $enum
     * @param bool $strictTypes
     */
    public function checkIncorrectValue($value, ?int $minLength, ?int $maxLength, ?array $enum, bool $strictTypes)
    {
        $rule = $this->getRule($minLength, $maxLength, $enum);
        try {
            $rule->validate(Action::input($strictTypes), '', $value);
            $this->fail('expect exception');
        } catch (InvalidValueException $exception) {
            $expectedError = false;
            foreach ($exception->getErrors() as $error) {
                if ($expectedError) {
                    continue;
                }
                if ($strictTypes && $error instanceof InvalidType) {
                    $expectedError = true;
                }
                if ($enum && $error instanceof InvalidEnumValue) {
                    $expectedError = true;
                }
                if (($minLength || $maxLength) && $error instanceof InvalidStringSize) {
                    $expectedError = true;
                }
                if ($error instanceof InvalidFormat) {
                    $expectedError = true;
                }
            }
            $this->assertTrue($expectedError);
        }
    }

    private function getRule(?int $minLength, ?int $maxLength, ?array $enum): StringRule
    {
        $actions = [Rule::ACTION_READ, Rule::ACTION_WRITE];
        $format = new VoidFormat();
        return new StringRule($format, $actions, $minLength, $maxLength, true, $enum);
    }

    private function getStringObject(): object
    {
        return new class {

            public function __toString(): string
            {
                return 'value';
            }
        };
    }
}
