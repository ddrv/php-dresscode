<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Rule;

final class AnyRule extends Rule
{

    public function __construct(array $actions, bool $nullable)
    {
        parent::__construct($actions, $nullable);
    }

    /**
     * @inheritDoc
     */
    protected function check(Action $action, string $path, $value)
    {
        return $value;
    }
}
