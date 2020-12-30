<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidNumberSize;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Rule;
use Ddrv\DressCode\Rule\NumberRule;
use PHPUnit\Framework\TestCase;

final class NumberRuleTest extends TestCase
{

    /**
     * @param mixed $value
     * @param float|null $minimal
     * @param float|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @throws InvalidValueException
     * @dataProvider provideCorrectValues
     */
    public function testCorrectValue(
        $value,
        ?float $minimal,
        ?float $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal
    ) {
        $this->checkCorrect($value, $minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal, false);
    }

    /**
     * @param mixed $value
     * @param float|null $minimal
     * @param float|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @dataProvider provideIncorrectValues
     */
    public function testIncorrectValue(
        $value,
        ?float $minimal,
        ?float $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal
    ) {
        $this->checkIncorrect($value, $minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal, false);
    }

    /**
     * @param mixed $value
     * @param float|null $minimal
     * @param float|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @throws InvalidValueException
     * @dataProvider provideStrictCorrectValues
     */
    public function testStrictCorrectValue(
        $value,
        ?float $minimal,
        ?float $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal
    ) {
        $this->checkCorrect($value, $minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal, true);
    }

    /**
     * @param mixed $value
     * @param float|null $minimal
     * @param float|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @dataProvider provideStrictIncorrectValues
     */
    public function testStrictIncorrectValue(
        $value,
        ?float $minimal,
        ?float $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal
    ) {
        $this->checkIncorrect($value, $minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal, true);
    }

    public function provideCorrectValues(): array
    {
        return [
            [5000,   null, null, false, false],
            [-500,   null, null, false, false],
            ['50',   null, null, false, false],
            ['-5',   null, null, false, false],
            ['-0',   -500, null, false, false],
            ['-5',   -5,   null, false, false],
            ['-4',   -5,   null, true,  false],
            ['-4.9', -5,   null, true,  false],
            ['50',   50,   null, false, false],
            ['6',    5,    null, true,  false],
            ['0',    null, 1,    false, false],
            ['-5',   null, -5,   false, false],
            ['-6',   null, -5,   false, true ],
            ['5',    null, 5,    false, false],
            ['4',    null, 5,    false, true ],
            ['4',    3,    5,    true,  true ],
            [8,      null, null, true,  true ],
            [0,      null, null, true,  true ],
            [-4,     null, null, true,  true ],
            [-.05,   null, null, true,  true ],
            [.4,     null, null, true,  true ],
            [0.4,    null, null, true,  true ],
            ['0.4',  null, null, true,  true ],
            ['.4',   null, null, true,  true ],
            ['-.4',  null, null, true,  true ],
            ['-0.4', null, null, true,  true ],
            ['.0',   null, null, true,  true ],
            ['0.0',  null, null, true,  true ],
        ];
    }

    public function provideIncorrectValues(): array
    {
        return [
            ['aa', null, null, false, false],
            [50.1, 50.1, null, true,  true ],
            [50.1, null, 50.1, true,  true ],
            [50.1, 50.2, null, false, false],
            [50.2, null, 50.1, false, false],
        ];
    }

    public function provideStrictCorrectValues(): array
    {
        return [
            [5000, null, null, false, false],
            [-500, null, null, false, false],
            [0,    -500, null, false, false],
            [-5,   -5,   null, false, false],
            [-4,   -5,   null, true,  false],
            [-4.9, -5,   null, true,  false],
            [50,   50,   null, false, false],
            [6,    5,    null, true,  false],
            [0,    null, 1,    false, false],
            [-5,   null, -5,   false, false],
            [-6,   null, -5,   false, true ],
            [5,    null, 5,    false, false],
            [4,    null, 5,    false, true ],
            [4,    3,    5,    true,  true ],
            [8,    null, null, true,  true ],
            [0,    null, null, true,  true ],
            [-4,   null, null, true,  true ],
            [-.05, null, null, true,  true ],
            [.4,   null, null, true,  true ],
            [0.4,  null, null, true,  true ],
            [-.4,  null, null, true,  true ],
            [.0,   null, null, true,  true ],
        ];
    }

    public function provideStrictIncorrectValues(): array
    {
        return [
            ['a2bc', null, null, false, false],
            ['50.1', 50.1, null, true,  true ],
            ['5000', null, 50.1, true,  true ],
            ['0.11', 50.2, null, false, false],
            ['.002', null, 50.1, false, false],
        ];
    }

    private function getRule(
        ?float $minimal,
        ?float $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal
    ): NumberRule {
        $actions = [Rule::ACTION_READ, Rule::ACTION_WRITE];
        return new NumberRule(
            $actions,
            $minimal,
            $maximal,
            $exclusiveMinimal,
            $exclusiveMaximal,
            null,
            true,
            null
        );
    }

    /**
     * @param mixed $value
     * @param float|null $minimal
     * @param float|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @param bool $strictTypes
     * @throws InvalidValueException
     */
    public function checkCorrect(
        $value,
        ?float $minimal,
        ?float $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        bool $strictTypes
    ) {
        $rule = $this->getRule($minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal);
        $result = $rule->validate(Action::input($strictTypes), '', $value);
        $this->assertSame((float)$value, $result[0]);
    }

    /**
     * @param mixed $value
     * @param float|null $minimal
     * @param float|null $maximal
     * @param bool $exclusiveMinimal
     * @param bool $exclusiveMaximal
     * @param bool $strictTypes
     */
    private function checkIncorrect(
        $value,
        ?float $minimal,
        ?float $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        bool $strictTypes
    ) {
        $rule = $this->getRule($minimal, $maximal, $exclusiveMinimal, $exclusiveMaximal);
        try {
            $rule->validate(Action::input($strictTypes), '', $value);
            $this->fail('expect exception');
        } catch (InvalidValueException $exception) {
            $expectedError = false;
            foreach ($exception->getErrors() as $error) {
                if ($expectedError) {
                    continue;
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
}
