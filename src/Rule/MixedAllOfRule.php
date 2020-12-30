<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\InvalidValueException;

final class MixedAllOfRule extends MixedRule
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
                $result = $this->merge($result, $rule->validate($action, $path, $value)[0]);
                $success++;
            } catch (InvalidValueException $e) {
            }
        }
        if ($success === count($this->rules)) {
            return $result;
        }
        $error = new InvalidType($path, '\'all of\' polymorphic');
        throw new InvalidValueException($error);
    }

    /**
     * @param mixed $value1
     * @param mixed $value2
     * @return mixed
     */
    private function merge($value1, $value2)
    {
        if (is_null($value1)) {
            return $value2;
        }
        if (!is_array($value1) || !is_array($value2)) {
            return $value1;
        }
        return array_replace_recursive($value1, $value2);
    }
}
