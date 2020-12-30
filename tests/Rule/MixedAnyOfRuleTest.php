<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Rule\MixedAnyOfRule;
use Ddrv\DressCode\Rule\MixedRule;

final class MixedAnyOfRuleTest extends MixedRuleTestCase
{

    public function provideCorrectData(): array
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

    public function provideIncorrectData(): array
    {
        return [
            ['$'],
            ['*'],
            ['@'],
        ];
    }

    protected function createMixedRule(array $actions, array $rules, bool $nullable): MixedRule
    {
        return new MixedAnyOfRule($actions, $rules, $nullable);
    }
}
