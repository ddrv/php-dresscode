<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Issues;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\DressCode;
use Ddrv\DressCode\Exception\InvalidValueException;
use PHPUnit\Framework\TestCase;

abstract class IssueTestCase extends TestCase
{

    final public function testIssue()
    {
        $validator = new DressCode();
        $rule = json_decode($this->getRuleJson(), true);
        $value = json_decode($this->getValueJson(), true);
        $check = $this->getExpectedErrors();
        try {
            $validator->validate(Action::input(true), $rule, $value);
            $this->assertTrue(is_null($check));
        } catch (InvalidValueException $exception) {
            $this->assertFalse(is_null($check));
            $errors = [];
            foreach ($exception->getErrors() as $error) {
                $errors[$error->getPath()][] = $error->getToken();
            }
            $this->assertArray($check, $errors);
        }
    }

    private function assertArray(array $expected, array $actual)
    {
        $this->assertCount(count($expected), $actual);
        foreach ($expected as $key => $items) {
            $this->assertArrayHasKey($key, $actual);
            $this->assertCount(count($items), $actual[$key]);
            foreach ($items as $j => $item) {
                $this->assertSame($item, $actual[$key][$j]);
            }
        }
    }

    abstract protected function getRuleJson(): string;

    abstract protected function getValueJson(): string;

    abstract protected function getExpectedErrors(): ?array;
}
