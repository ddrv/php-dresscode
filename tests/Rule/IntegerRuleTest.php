<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidNumberSize;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\Error\NotMultipleOf;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Rule;
use Ddrv\DressCode\Rule\IntegerRule;
use PHPUnit\Framework\TestCase;

final class IntegerRuleTest extends TestCase
{

    /**
     * @param mixed $value
     * @param int|null $minimal
     * @param int|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @param int|null $multipleOf
     * @throws InvalidValueException
     * @dataProvider provideCorrectValues
     */
    public function testCorrectValue(
        $value,
        ?int $minimal,
        ?int $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        ?int $multipleOf
    ) {
        $this->checkCorrectData($value, $minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal, $multipleOf, false);
    }

    /**
     * @param mixed $value
     * @param int|null $minimal
     * @param int|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @param int|null $multipleOf
     * @throws InvalidValueException
     * @dataProvider provideStrictCorrectValues
     */
    public function testStrictCorrectValue(
        $value,
        ?int $minimal,
        ?int $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        ?int $multipleOf
    ) {
        $this->checkCorrectData($value, $minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal, $multipleOf, true);
    }

    /**
     * @param mixed $value
     * @param int|null $minimal
     * @param int|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @param int|null $multipleOf
     * @dataProvider provideIncorrectValues
     */
    public function testIncorrectValue(
        $value,
        ?int $minimal,
        ?int $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        ?int $multipleOf
    ) {
        $this->checkIncorrectData($value, $minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal, $multipleOf, false);
    }

    /**
     * @param mixed $value
     * @param int|null $minimal
     * @param int|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @param int|null $multipleOf
     * @dataProvider provideStrictIncorrectValues
     */
    public function testStrictIncorrectValue(
        $value,
        ?int $minimal,
        ?int $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        ?int $multipleOf
    ) {
        $this->checkIncorrectData($value, $minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal, $multipleOf, true);
    }

    public function provideCorrectValues(): array
    {
        return [
            [5000, null, null, false, false, null],
            [-500, null, null, false, false, null],
            ['50', null, null, false, false, null],
            ['-5', null, null, false, false, null],
            ['-0', -500, null, false, false, null],
            ['-5', -5,   null, false, false, null],
            ['-4', -5,   null, true,  false, null],
            ['50', 50,   null, false, false, null],
            ['6',  5,    null, true,  false, null],
            ['0',  null, 1,    false, false, null],
            ['-5', null, -5,   false, false, null],
            ['-6', null, -5,   false, true,  null],
            ['5',  null, 5,    false, false, null],
            ['4',  null, 5,    false, true,  null],
            ['4',  3,    5,    true,  true,  null],
            [8,    null, null, true,  true,  4],
            [4,    null, null, true,  true,  4],
            [0,    null, null, true,  true,  4],
            [-4,   null, null, true,  true,  4],
        ];
    }

    public function provideIncorrectValues(): array
    {
        return [
            ['aa', null, null, false, false, null],
            [5.01, null, null, false, false, null],
            [1001, null, null, true,  true,  4],
            [1002, null, null, true,  true,  4],
            [1003, null, null, true,  true,  4],
            [-503, null, null, true,  true,  4],
            [5000, 5000, null, true,  true,  null],
            [5000, null, 5000, true,  true,  null],
        ];
    }

    public function provideStrictCorrectValues(): array
    {
        return [
            [5000, null, null, false, false, null],
            [-500, null, null, false, false, null],
            [-400, -500, null, false, false, null],
            [-500, -500, null, false, false, null],
            [-499, -500, null, true,  false, null],
            [5001, 5000, null, true,  false, null],
            [0,    null, 1000, false, false, null],
            [-500, null, -500, false, false, null],
            [-501, null, -500, false, true,  null],
            [5000, null, 5000, false, false, null],
            [4999, null, 5000, false, true,  null],
            [3001, 3000, 3002, true,  true,  null],
            [8,    null, null, true,  true,  4],
            [4,    null, null, true,  true,  4],
            [0,    null, null, true,  true,  4],
            [-4,   null, null, true,  true,  4],
        ];
    }

    public function provideStrictIncorrectValues(): array
    {
        return [
            ['a2cd', null, null, false, false, null],
            ['5.01', null, null, false, false, null],
            ['1001', null, null, true,  true,  4],
            ['1002', null, null, true,  true,  4],
            ['1003', null, null, true,  true,  4],
            ['-503', null, null, true,  true,  4],
            ['5000', 5000, null, true,  true,  null],
            ['5000', null, 5000, true,  true,  null],
        ];
    }

    /**
     * @param $value
     * @param int|null $minimal
     * @param int|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @param int|null $multipleOf
     * @param bool $strictTypes
     * @throws InvalidValueException
     */
    private function checkCorrectData(
        $value,
        ?int $minimal,
        ?int $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        ?int $multipleOf,
        bool $strictTypes
    ) {
        $rule = $this->getRule($minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal, $multipleOf);
        $result = $rule->validate(Action::input($strictTypes), '', $value);
        $this->assertSame((int)$value, $result[0]);
    }

    /**
     * @param mixed $value
     * @param int|null $minimal
     * @param int|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @param int|null $multipleOf
     * @param bool $strictTypes
     */
    private function checkIncorrectData(
        $value,
        ?int $minimal,
        ?int $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        ?int $multipleOf,
        bool $strictTypes
    ) {
        $rule = $this->getRule($minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal, $multipleOf);
        try {
            $rule->validate(Action::input($strictTypes), '', $value);
            $this->fail('expect exception');
        } catch (InvalidValueException $exception) {
            $expectedError = false;
            foreach ($exception->getErrors() as $error) {
                if ($expectedError) {
                    continue;
                }
                if (!is_null($multipleOf) && $error instanceof NotMultipleOf) {
                    $expectedError = true;
                }
                if (($minimal || $maximal) && $error instanceof InvalidNumberSize) {
                    $expectedError = true;
                }
                if ($error instanceof InvalidType) {
                    $expectedError = true;
                }
            }
            $this->assertTrue($expectedError);
        }
    }

    private function getRule(
        ?int $minimal,
        ?int $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        ?int $multipleOf
    ): IntegerRule {
        $actions = [Rule::ACTION_READ, Rule::ACTION_WRITE];
        return new IntegerRule(
            $actions,
            $minimal,
            $maximal,
            $exclusiveMinimal,
            $exclusiveMaximal,
            $multipleOf,
            true,
            null
        );
    }
}
