<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\NotNullableValue;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Rule;
use PHPUnit\Framework\TestCase;

final class SomeRuleTest extends TestCase
{

    public function testRWInput()
    {
        $rule = $this->getRule(true, true);
        $result = $rule->validate(Action::input(), '', 'value');
        $this->assertTrue($result[1]);
    }

    public function testRWOutput()
    {
        $rule = $this->getRule(true, true);
        $result = $rule->validate(Action::output(), '', 'value');
        $this->assertTrue($result[1]);
    }

    public function testReadOnlyInput()
    {
        $rule = $this->getRule(true, false);
        $result = $rule->validate(Action::input(), '', 'value');
        $this->assertFalse($result[1]);
    }

    public function testReadOnlyOutput()
    {
        $rule = $this->getRule(true, false);
        $result = $rule->validate(Action::output(), '', 'value');
        $this->assertTrue($result[1]);
    }

    public function testWriteOnlyInput()
    {
        $rule = $this->getRule(false, true);
        $result = $rule->validate(Action::input(), '', 'value');
        $this->assertTrue($result[1]);
    }

    public function testWriteOnlyOutput()
    {
        $rule = $this->getRule(false, true);
        $result = $rule->validate(Action::output(), '', 'value');
        $this->assertFalse($result[1]);
    }

    public function testNullOnNullable()
    {
        $rule = $this->getRule(true, true);
        $result = $rule->validate(Action::output(), '', null);
        $this->assertNull($result[0]);
        $this->assertTrue($result[1]);
    }

    public function testNullOnNotNullable()
    {
        $rule = $this->getRule(true, true, false);
        try {
            $rule->validate(Action::output(), '', null);
            $this->fail('expect exception');
        } catch (InvalidValueException $exception) {
            $expectedError = false;
            foreach ($exception->getErrors() as $error) {
                if ($expectedError) {
                    continue;
                }
                if ($error instanceof NotNullableValue) {
                    $expectedError = true;
                }
            }
            $this->assertTrue($expectedError);
        }
    }

    protected function getRule(bool $read, bool $write, bool $nullable = true): Rule
    {
        $actions = [];
        if ($read) {
            $actions[] = Rule::ACTION_READ;
        }
        if ($write) {
            $actions[] = Rule::ACTION_WRITE;
        }
        return new class ($actions, $nullable) extends Rule
        {

            public function __construct(array $actions, bool $nullable)
            {
                parent::__construct($actions, $nullable);
            }

            protected function check(Action $action, string $path, $value)
            {
                return $value;
            }
        };
    }
}
