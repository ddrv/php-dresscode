<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidNumberSize;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\Error\NotMultipleOf;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Rule;

abstract class NumericRule extends Rule
{

    /**
     * @var int|float|null
     */
    private $minimal;

    /**
     * @var int|float|null
     */
    private $maximal;

    /**
     * @var bool
     */
    private $exclusiveMinimal;

    /**
     * @var bool
     */
    private $exclusiveMaximal;

    /**
     * @var int|float
     */
    private $multipleOf;

    /**
     * @var string|null
     */
    private $format;

    public function __construct(
        array $actions,
        $minimal,
        $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        $multipleOf,
        bool $nullable,
        ?string $format
    ) {
        parent::__construct($actions, $nullable);
        $this->minimal = $minimal;
        $this->maximal = $maximal;
        $this->exclusiveMinimal = $exclusiveMinimal;
        $this->exclusiveMaximal = $exclusiveMaximal;
        $this->multipleOf = $multipleOf;
        $this->format = $format;
    }

    /**
     * @inheritDoc
     */
    protected function check(Action $action, string $path, $value)
    {
        if (!$this->checkType($action->isStrictTypes(), $value)) {
            throw new InvalidValueException(new InvalidType($path, $this->getType()));
        }
        if ($this->format && !$this->checkFormat($this->format, $value)) {
            throw new InvalidValueException(new InvalidType($path, $this->getType()));
        }
        $errors = [];
        $withFrom = !$this->exclusiveMinimal;
        $withTo = !$this->exclusiveMaximal;
        $ok = true;
        if (!is_null($this->minimal) && $value < $this->minimal) {
            $ok = false;
        }
        if (!is_null($this->minimal) && $this->exclusiveMinimal && $value == $this->minimal) {
            $ok = false;
        }
        if (!is_null($this->maximal) && $value > $this->maximal) {
            $ok = false;
        }
        if (!is_null($this->maximal) && $this->exclusiveMaximal && $value == $this->maximal) {
            $ok = false;
        }
        if (!$ok) {
            $errors[] = new InvalidNumberSize($path, $this->minimal, $this->maximal, $withFrom, $withTo);
        }
        if (!is_null($this->multipleOf) && ($value % $this->multipleOf) !== 0) {
            $errors[] = new NotMultipleOf($path, $this->multipleOf);
        }
        $this->throw($errors);
        return $this->toType($value);
    }

    /**
     * @param bool $strictTypes
     * @param mixed $value
     * @return bool
     */
    abstract protected function checkType(bool $strictTypes, $value): bool;

    /**
     * @return string
     */
    abstract protected function getType(): string;

    /**
     * @param string $format
     * @param mixed $value
     * @return bool
     */
    abstract protected function checkFormat(?string $format, $value): bool;

    /**
     * @param $value
     * @return int|float
     */
    abstract protected function toType($value);
}
