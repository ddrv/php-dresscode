<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Ddrv\DressCode\Rule;

abstract class MixedRule extends Rule
{

    /**
     * @var Rule[]
     */
    protected $rules;

    /**
     * @param array $actions
     * @param Rule[] $rules
     * @param bool $nullable
     */
    public function __construct(array $actions, array $rules, bool $nullable)
    {
        parent::__construct($actions, $nullable);
        $this->rules = $rules;
    }
}
