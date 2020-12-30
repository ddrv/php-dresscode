<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Rule;

use Ddrv\DressCode\Rule\MixedOneOfRule;
use Ddrv\DressCode\Rule\MixedRule;

final class MixedOneOfRuleTest extends MixedRuleTestCase
{

    public function provideCorrectData(): array
    {
        return [
            ['abc'],
            ['123'],
            ['-._'],
        ];
    }

    public function provideIncorrectData(): array
    {
        return [
            ['ab1'],
            ['12_'],
            ['-1_'],
            ['-a_'],
            ['-a1'],
        ];
    }

    protected function createMixedRule(array $actions, array $rules, bool $nullable): MixedRule
    {
        return new MixedOneOfRule($actions, $rules, $nullable);
    }
}
