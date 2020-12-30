<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\InvalidValueException;

final class MixedAnyOfRule extends MixedRule
{

    /**
     * @inheritDoc
     */
    protected function check(Action $action, string $path, $value)
    {
        foreach ($this->rules as $rule) {
            try {
                return $rule->validate($action, $path, $value)[0];
            } catch (InvalidValueException $e) {
            }
        }
        $error = new InvalidType($path, '\'any of\' polymorphic');
        throw new InvalidValueException($error);
    }
}
