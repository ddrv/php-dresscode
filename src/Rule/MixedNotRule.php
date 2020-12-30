<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\InvalidValueException;

final class MixedNotRule extends MixedRule
{

    /**
     * @inheritDoc
     */
    protected function check(Action $action, string $path, $value)
    {
        $fail = false;
        foreach ($this->rules as $rule) {
            try {
                $rule->validate($action, $path, $value);
                $fail = true;
            } catch (InvalidValueException $e) {
            }
        }
        if ($fail) {
            $error = new InvalidType($path, '\'not\' polymorphic');
            throw new InvalidValueException($error);
        }
        return $value;
    }
}
