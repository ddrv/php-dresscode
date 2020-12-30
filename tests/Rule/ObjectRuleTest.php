<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\FormatManager;
use Ddrv\DressCode\Rule;
use Ddrv\DressCode\Rule\ObjectRule;
use Ddrv\DressCode\RuleManager;
use PHPUnit\Framework\TestCase;

final class ObjectRuleTest extends TestCase
{

    /**
     * @param array $element
     * @param array|null $additionalProperties
     * @throws InvalidValueException
     * @dataProvider provideCorrectModel
     */
    public function testCorrectValue(array $element, ?array $additionalProperties)
    {
        $rule = $this->getRule(null, null, $additionalProperties);
        $result = $rule->validate(Action::input(), '', $element)[0];
        $this->assertArray($element, $result);
    }

    private function assertArray(array $expected, array $actual)
    {
        $this->assertCount(count($expected), $actual);
        foreach ($expected as $key => $item) {
            $this->assertArrayHasKey($key, $actual);
            $this->assertSame($item, $actual[$key]);
        }
    }

    private function getRule(?int $minProperties, ?int $maxProperties, ?array $additionalProperties): ObjectRule
    {
        $formatManager = new FormatManager();
        $ruleManager = new RuleManager($formatManager);
        $actions = [Rule::ACTION_READ, Rule::ACTION_WRITE];
        $properties = [
            'str' => [
                'type' => 'string',
            ],
            'int' => [
                'type' => 'integer',
            ],
            'bool' => [
                'type' => 'boolean',
            ],
        ];
        $required = ['str', 'int'];

        return new ObjectRule(
            $ruleManager,
            $actions,
            $properties,
            $required,
            $additionalProperties,
            $minProperties,
            $maxProperties,
            true
        );
    }

    public function provideCorrectModel(): array
    {
        return [
            [['str' => 'phpunit', 'int' => 1],                 null],
            [['str' => 'phpunit', 'int' => 1, 'bool' => true], null],
        ];
    }
}
