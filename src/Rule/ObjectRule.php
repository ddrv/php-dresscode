<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use ArrayAccess;
use Countable;
use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\Error\InvalidObjectSize;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\Error\ObjectHasNotRequiredProperty;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Rule;
use Ddrv\DressCode\RuleManager;

final class ObjectRule extends Rule
{

    /**
     * @var RuleManager
     */
    private $ruleManager;

    /**
     * @var array[]|null
     */
    private $properties;

    /**
     * @var string[]|null
     */
    private $required;

    /**
     * @var bool
     */
    private $additionalProperties;

    /**
     * @var int|null
     */
    private $minProperties;

    /**
     * @var int|null
     */
    private $maxProperties;

    public function __construct(
        RuleManager $ruleManager,
        array $actions,
        ?array $properties,
        ?array $required,
        ?array $additionalProperties,
        ?int $minProperties,
        ?int $maxProperties,
        bool $nullable
    ) {
        parent::__construct($actions, $nullable);
        $this->ruleManager = $ruleManager;
        $this->properties = $properties;
        $this->required = $required;
        $this->additionalProperties = $additionalProperties;
        $this->minProperties = $minProperties;
        $this->maxProperties = $maxProperties;
    }

    /**
     * @inheritDoc
     */
    protected function check(Action $action, string $path, $value): array
    {
        if (!is_iterable($value) && !$value instanceof Countable) {
            throw new InvalidValueException(new InvalidType($path, 'object'));
        }
        $size = count($value);
        $errors = [];
        $result = [];
        $least = $this->minProperties && $size < $this->minProperties;
        $upTo = $this->maxProperties && $size > $this->maxProperties;
        if ($least || $upTo) {
            $errors[] = new InvalidObjectSize($path, $this->minProperties, $this->maxProperties);
        }
        if ($this->required) {
            foreach ($this->required as $property) {
                if (!$this->hasProperty($value, $property)) {
                    $errors[] = new ObjectHasNotRequiredProperty($path, $property);
                }
            }
        }

        $additional = [];
        foreach ($value as $property => $v) {
            $additional[$property] = true;
        }
        if ($this->properties) {
            foreach ($this->properties as $property => $definition) {
                if ($this->hasProperty($value, $property)) {
                    if (array_key_exists($property, $additional)) {
                        unset($additional[$property]);
                    }
                    $propertyPath = $path ? $path . '.' . $property : $property;
                    $rule = $this->ruleManager->getRule($definition);
                    $default = array_key_exists('default', $definition) ? $definition['default'] : null;
                    try {
                        [$valid, $add] = $rule->validate(
                            $action,
                            $propertyPath,
                            $this->getProperty($value, $property, $default)
                        );
                        if ($add) {
                            $result[$property] = $valid;
                        }
                    } catch (InvalidValueException $e) {
                        array_splice($errors, count($errors), 0, $e->getErrors());
                    }
                }
            }
        }

        if ($this->additionalProperties && $additional) {
            $rule = $this->ruleManager->getRule($this->additionalProperties);
            foreach ($additional as $property) {
                $propertyPath = $path ? $path . '.' . $property : $property;
                $result[$property] = $rule->validate($action, $propertyPath, $this->getProperty($value, $property));
            }
        }
        $this->throw($errors);
        return $result;
    }

    private function hasProperty($object, string $property): bool
    {
        if (is_array($object) || $object instanceof ArrayAccess) {
            return array_key_exists($property, $object);
        }
        if (is_object($object)) {
            return property_exists($object, $property);
        }
        return false;
    }

    private function getProperty($object, string $property, $default = null)
    {
        if (is_array($object) || $object instanceof ArrayAccess) {
            return array_key_exists($property, $object) ? $object[$property] : $default;
        }
        if (is_object($object)) {
            return property_exists($object, $property) ? $object->$property : $default;
        }
        return $default;
    }
}
