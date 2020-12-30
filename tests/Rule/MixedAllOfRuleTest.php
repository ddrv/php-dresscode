<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Rule\MixedAllOfRule;
use Ddrv\DressCode\Rule\MixedRule;

final class MixedAllOfRuleTest extends MixedRuleTestCase
{

    public function provideCorrectData(): array
    {
        return [
            ['-a1'],
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
        ];
    }

    protected function createMixedRule(array $actions, array $rules, bool $nullable): MixedRule
    {
        return new MixedAllOfRule($actions, $rules, $nullable);
    }
}
