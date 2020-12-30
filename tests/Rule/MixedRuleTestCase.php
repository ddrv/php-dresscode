<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\FormatManager;
use Ddrv\DressCode\Rule;
use Ddrv\DressCode\Rule\MixedRule;
use Ddrv\DressCode\RuleManager;
use PHPUnit\Framework\TestCase;

abstract class MixedRuleTestCase extends TestCase
{

    /**
     * @param string $value
     * @throws InvalidValueException
     * @dataProvider provideCorrectData
     */
    final public function testCorrect(string $value)
    {
        $result = $this->getMixedRule()->validate(Action::input(), '', $value)[0];
        $this->assertSame($value, $result);
    }

    /**
     * @param string $value
     * @throws InvalidValueException
     * @dataProvider provideIncorrectData
     */
    final public function testIncorrect(string $value)
    {
        $this->expectException(InvalidValueException::class);
        $this->getMixedRule()->validate(Action::input(), '', $value)[0];
    }

    abstract public function provideCorrectData(): array;

    abstract public function provideIncorrectData(): array;

    final protected function getMixedRule(): MixedRule
    {
        $rules = [];
        $ruleManager = $this->getRuleManager();
        foreach ($this->getRules() as $rule) {
            $rules[] = $ruleManager->getRule($rule);
        }
        $actions = [Rule::ACTION_READ, Rule::ACTION_WRITE];
        return $this->createMixedRule($actions, $rules, false);
    }

    abstract protected function createMixedRule(array $actions, array $rules, bool $nullable): MixedRule;

    private function getRules(): array
    {
        return [
            [
                'type' => 'string',
                'pattern' => '[0-9]+',
            ],
            [
                'type' => 'string',
                'pattern' => '[a-z]+',
            ],
            [
                'type' => 'string',
                'pattern' => '[\-\._]+',
            ],
        ];
    }

    private function getRuleManager(): RuleManager
    {
        return new RuleManager(new FormatManager());
    }
}
