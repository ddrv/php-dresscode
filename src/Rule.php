<?php

declare(strict_types=1);

namespace Ddrv\DressCode;

use Ddrv\DressCode\Exception\Error\NotNullableValue;
use Ddrv\DressCode\Exception\Error\ValueError;
use Ddrv\DressCode\Exception\InvalidValueException;

abstract class Rule
{

    public const ACTION_WRITE = 'w';
    public const ACTION_READ  = 'r';

    /**
     * @var bool
     */
    private $checkInput = false;

    /**
     * @var bool
     */
    private $checkOutput = false;

    /**
     * @var bool
     */
    private $nullable;

    public function __construct(array $actions, bool $nullable)
    {
        if (in_array(self::ACTION_READ, $actions)) {
            $this->checkOutput = true;
        }
        if (in_array(self::ACTION_WRITE, $actions)) {
            $this->checkInput = true;
        }
        if (!$this->checkOutput && !$this->checkInput) {
            $this->checkOutput = true;
            $this->checkInput = true;
        }
        $this->nullable = $nullable;
    }

    /**
     * @param Action $action
     * @param string $path
     * @param mixed $value
     * @return array
     * @throws InvalidValueException
     */
    final public function validate(Action $action, string $path, $value): array
    {
        if (($action->isInput() && !$this->checkInput) || ($action->isOutput() && !$this->checkOutput)) {
            return [null, false];
        }

        if (!$this->nullable && is_null($value)) {
            throw new InvalidValueException(new NotNullableValue($path));
        }
        if (is_null($value)) {
            return [null, true];
        }
        return [$this->check($action, $path, $value), true];
    }

    /**
     * @param Action $action
     * @param string $path
     * @param mixed $value
     * @return mixed
     * @throws InvalidValueException
     */
    abstract protected function check(Action $action, string $path, $value);

    /**
     * @param ValueError[] $errors
     * @throws InvalidValueException
     */
    final protected function throw(array $errors): void
    {
        $filtered = [];
        foreach ($errors as $error) {
            if (!$error instanceof ValueError) {
                continue;
            }
            $filtered[] = $error;
        }
        if (!$filtered) {
            return;
        }
        $first = array_shift($filtered);
        $exception = new InvalidValueException($first);
        foreach ($filtered as $error) {
            $exception->addError($error);
        }
        throw $exception;
    }
}
