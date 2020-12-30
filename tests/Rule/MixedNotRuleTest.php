<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Rule\MixedNotRule;
use Ddrv\DressCode\Rule\MixedRule;

final class MixedNotRuleTest extends MixedRuleTestCase
{

    public function provideCorrectData(): array
    {
        return [
            ['$'],
            ['*'],
            ['@'],
        ];
    }

    public function provideIncorrectData(): array
    {
        return [
            ['abc'],
            ['123'],
            ['-._'],
            ['ab1'],
            ['12_'],
            ['-1_'],
            ['-a_'],
            ['-a1'],
        ];
    }

    protected function createMixedRule(array $actions, array $rules, bool $nullable): MixedRule
    {
        return new MixedNotRule($actions, $rules, $nullable);
    }
}
