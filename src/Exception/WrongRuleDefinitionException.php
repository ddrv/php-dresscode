<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception;

final class WrongRuleDefinitionException extends \RuntimeException
{

    public function __construct()
    {
        parent::__construct('wrong rule definition', 1);
    }
}
