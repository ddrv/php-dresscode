<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidEnumValue;
use Ddrv\DressCode\Exception\Error\InvalidFormat;
use Ddrv\DressCode\Exception\Error\InvalidStringSize;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Exception\WrongFormatException;
use Ddrv\DressCode\Format\Format;
use Ddrv\DressCode\Rule;
use Throwable;

final class StringRule extends Rule
{

    /**
     * @var Format
     */
    private $format;

    /**
     * @var int|null
     */
    private $minLength;

    /**
     * @var int|null
     */
    private $maxLength;

    /**
     * @var string[]|null
     */
    private $enum;

    public function __construct(
        Format $format,
        array $actions,
        ?int $minLength,
        ?int $maxLength,
        bool $nullable,
        ?array $enum
    ) {
        parent::__construct($actions, $nullable);
        $this->format = $format;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->enum = $enum;
    }

    /**
     * @inheritDoc
     */
    protected function check(Action $action, string $path, $value): string
    {
        $value = $this->checkType($action->isStrictTypes(), $path, $value);
        $errors = [];
        $len = strlen($value);
        if (($this->minLength && $len < $this->minLength) || ($this->maxLength && $len > $this->maxLength)) {
            $errors[] = new InvalidStringSize($path, $this->minLength, $this->maxLength);
        }
        if ($this->format) {
            try {
                $this->format->check($value);
            } catch (WrongFormatException $e) {
                $format = $e->getFormat();
                $pattern = null;
                if (strpos($format, 'pattern:') === 0) {
                    $format = substr($format, 0, 8);
                    $pattern = substr($format, 8);
                }
                $errors[] = new InvalidFormat($path, $e->getMessage(), $format, $pattern);
            }
        }
        if ($this->enum && !in_array($value, $this->enum)) {
            $errors[] = new InvalidEnumValue($path, $this->enum);
        }
        $this->throw($errors);
        return $value;
    }

    /**
     * @param bool $strictTypes
     * @param string $path
     * @param mixed $value
     * @return string
     * @throws InvalidValueException
     */
    private function checkType(bool $strictTypes, string $path, $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_object($value) && method_exists($value, '__toString')) {
            try {
                return $value->__toString();
            } catch (Throwable $exception) {
                throw new InvalidValueException(new InvalidType($path, 'string'));
            }
        }
        if ($strictTypes) {
            throw new InvalidValueException(new InvalidType($path, 'string'));
        }
        return (string)$value;
    }
}
