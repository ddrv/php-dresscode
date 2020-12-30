<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Rule;

final class BooleanRule extends Rule
{

    public function __construct(array $actions, bool $nullable)
    {
        parent::__construct($actions, $nullable);
    }

    /**
     * @inheritDoc
     */
    protected function check(Action $action, string $path, $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if ($action->isStrictTypes() || !in_array($value, ['1', '0', ''])) {
            throw new InvalidValueException(new InvalidType($path, 'boolean'));
        }
        return $value === '1';
    }
}
