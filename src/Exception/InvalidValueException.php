<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception;

use Ddrv\DressCode\Exception\Error\ValueError;
use Exception;

final class InvalidValueException extends Exception
{

    /**
     * @var ValueError[]
     */
    private $errors = [];

    public function __construct(ValueError $error)
    {
        parent::__construct('invalid value', 1);
        $this->errors[] = $error;
    }

    public function addError(ValueError $error): self
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * @return ValueError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
