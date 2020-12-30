<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\FormatManager;
use Ddrv\DressCode\Rule;
use Ddrv\DressCode\Rule\ArrayRule;
use Ddrv\DressCode\RuleManager;
use PHPUnit\Framework\TestCase;

final class ArrayRuleTest extends TestCase
{

    /**
     * @param array $items
     * @param int|null $min
     * @param int|null $max
     * @param bool $unique
     * @throws InvalidValueException
     * @dataProvider provideCorrectData
     */
    public function testCorrectValue(array $items, ?int $min, ?int $max, bool $unique)
    {
        $rule = $this->getRule($min, $max, $unique);
        $result = $rule->validate(Action::input(), '', $items)[0];
        $this->assertArray($items, $result);
    }

    /**
     * @param array $items
     * @param int|null $min
     * @param int|null $max
     * @param bool $unique
     * @param array $errors
     * @dataProvider provideIncorrectData
     */
    public function testIncorrectValue(array $items, ?int $min, ?int $max, bool $unique, array $errors)
    {
        $rule = $this->getRule($min, $max, $unique);
        try {
            $rule->validate(Action::input(), 'array', $items)[0];
        } catch (InvalidValueException $exception) {
            $this->checkInvalidValueErrors($exception, $errors);
        }
    }

    private function checkInvalidValueErrors(InvalidValueException $exception, array $check)
    {
        $errors = [];
        foreach ($exception->getErrors() as $error) {
            $errors[$error->getPath()] = $error->getToken();
        }
        $this->assertArray($check, $errors);
    }

    private function assertArray(array $expected, array $actual)
    {
        $this->assertCount(count($expected), $actual);
        foreach ($expected as $key => $item) {
            $this->assertArrayHasKey($key, $actual);
            $this->assertSame($item, $actual[$key]);
        }
    }

    private function getRule(?int $minItems, ?int $maxItems, bool $uniqueItems): ArrayRule
    {
        $formatManager = new FormatManager();
        $ruleManager = new RuleManager($formatManager);
        $actions = [Rule::ACTION_READ, Rule::ACTION_WRITE];
        $items = [
            'type' => 'integer',
        ];
        return new ArrayRule($ruleManager, $actions, $items, $minItems, $maxItems, $uniqueItems, true);
    }

    public function provideCorrectData(): array
    {
        return [
            [[1, 2, 3], null, null, false],
            [[1, 1, 1], null, null, false],
            [[1, 2, 3], 3,    null, false],
            [[1, 2, 3], null, 3,    false],
            [[1, 2, 3], null, null, true],
        ];
    }

    public function provideIncorrectData(): array
    {
        $checks = [
            ['array.0' => InvalidType::TOKEN_INVALID_TYPE, 'array.2' => InvalidType::TOKEN_INVALID_TYPE],
            ['array.1' => InvalidType::TOKEN_NOT_UNIQUE_ITEMS, 'array.2' => InvalidType::TOKEN_NOT_UNIQUE_ITEMS],
            ['array' => InvalidType::TOKEN_INVALID_ARRAY_SIZE],
            ['array.1' => InvalidType::TOKEN_NOT_UNIQUE_ITEMS, 'array.2' => InvalidType::TOKEN_INVALID_TYPE],
        ];
        return [
            [['a', 100, 'c'], null, null, false, $checks[0]],
            [[100, 100, 100], null, null, true , $checks[1]],
            [[100, 200, 300], 4,    null, false, $checks[2]],
            [[100, 200, 300], null, 2,    false, $checks[2]],
            [[100, 100, 'a'], null, null, true , $checks[3]],
        ];
    }
}
