<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\ValueError;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Rule;
use Ddrv\DressCode\Rule\BooleanRule;
use PHPUnit\Framework\TestCase;

final class BooleanRuleTest extends TestCase
{

    public function testTrueValue()
    {
        $rule = $this->getRule();
        $result = $rule->validate(Action::input(), '', true);
        $this->assertTrue($result[0]);
        $result = $rule->validate(Action::input(), '', '1');
        $this->assertTrue($result[0]);
    }

    public function testFalseValue()
    {
        $rule = $this->getRule();
        $result = $rule->validate(Action::input(), '', false);
        $this->assertFalse($result[0]);
        $result = $rule->validate(Action::input(), '', '0');
        $this->assertFalse($result[0]);
        $result = $rule->validate(Action::input(), '', '');
        $this->assertFalse($result[0]);
    }

    public function testStrictTrueValue()
    {
        $rule = $this->getRule();
        $result = $rule->validate(Action::input(true), '', true);
        $this->assertTrue($result[0]);
        $this->expectException(InvalidValueException::class);
        $rule->validate(Action::input(true), '', '1');
    }

    public function testStrictFalseValue()
    {
        $rule = $this->getRule();
        $result = $rule->validate(Action::input(true), '', false);
        $this->assertFalse($result[0]);
        try {
            $rule->validate(Action::input(true), '', '0');
            $this->fail('expect exception');
        } catch (InvalidValueException $exception) {
            $errors = $exception->getErrors();
            $this->assertCount(1, $errors);
            $this->assertSame(ValueError::TOKEN_INVALID_TYPE, $errors[0]->getToken());
        }
        try {
            $rule->validate(Action::input(true), '', '');
            $this->fail('expect exception');
        } catch (InvalidValueException $exception) {
            $errors = $exception->getErrors();
            $this->assertCount(1, $errors);
            $this->assertSame(ValueError::TOKEN_INVALID_TYPE, $errors[0]->getToken());
        }
    }

    protected function getRule(): BooleanRule
    {
        $actions = [Rule::ACTION_READ, Rule::ACTION_WRITE];
        return new BooleanRule($actions, false);
    }
}
