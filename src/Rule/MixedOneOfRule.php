<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\InvalidValueException;

final class MixedOneOfRule extends MixedRule
{

    /**
     * @inheritDoc
     */
    protected function check(Action $action, string $path, $value)
    {
        $success = 0;
        $result = null;
        foreach ($this->rules as $rule) {
            try {
                $result = $rule->validate($action, $path, $value)[0];
                $success++;
            } catch (InvalidValueException $e) {
            }
        }
        if ($success === 1) {
            return $result;
        }
        $error = new InvalidType($path, '\'one of\' polymorphic');
        throw new InvalidValueException($error);
    }
}
