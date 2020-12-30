<?php

declare(strict_types=1);

namespace Ddrv\DressCode;

use Ddrv\DressCode\Exception\WrongRuleDefinitionException;
use Ddrv\DressCode\Rule\AnyRule;
use Ddrv\DressCode\Rule\ArrayRule;
use Ddrv\DressCode\Rule\BooleanRule;
use Ddrv\DressCode\Rule\IntegerRule;
use Ddrv\DressCode\Rule\MixedAllOfRule;
use Ddrv\DressCode\Rule\MixedAnyOfRule;
use Ddrv\DressCode\Rule\MixedNotRule;
use Ddrv\DressCode\Rule\MixedOneOfRule;
use Ddrv\DressCode\Rule\NumberRule;
use Ddrv\DressCode\Rule\ObjectRule;
use Ddrv\DressCode\Rule\StringRule;

final class RuleManager
{

    /**
     * @var FormatManager
     */
    private $formats;

    /**
     * @var Rule[]
     */
    private $cache = [];

    /**
     * @var Rule[]
     */
    private $rules = [];

    public function __construct(FormatManager $formatManager)
    {
        $this->formats = $formatManager;
    }

    public function setEntity(string $name, array $definition)
    {
        $rule = $this->getRule($definition);
        $this->rules[$name] = $rule;
    }

    public function getRule(array $definition): Rule
    {
        if (array_key_exists('$ref', $definition)) {
            if (is_string($definition['$ref']) && array_key_exists($definition['$ref'], $this->rules)) {
                return $this->rules[$definition['$ref']];
            }
        }
        $nullable = array_key_exists('nullable', $definition) ? (bool)$definition['nullable'] : false;
        $readOnly = array_key_exists('readOnly', $definition) ? (bool)$definition['readOnly'] : false;
        $writeOnly = array_key_exists('writeOnly', $definition) ? (bool)$definition['writeOnly'] : false;
        $actions = [
            Rule::ACTION_READ,
            Rule::ACTION_WRITE,
        ];
        if ($readOnly && !$writeOnly) {
            $actions = [Rule::ACTION_READ];
        }
        if (!$readOnly && $writeOnly) {
            $actions = [Rule::ACTION_WRITE];
        }
        if (array_key_exists('oneOf', $definition)) {
            return $this->createOneOf($definition, $actions, $nullable);
        }
        if (array_key_exists('allOf', $definition)) {
            return $this->createAllOf($definition, $actions, $nullable);
        }
        if (array_key_exists('anyOf', $definition)) {
            return $this->createAnyOf($definition, $actions, $nullable);
        }
        if (array_key_exists('not', $definition)) {
            return $this->createNot($definition, $actions, $nullable);
        }
        if (array_key_exists('type', $definition) && !is_string($definition['type'])) {
            throw new WrongRuleDefinitionException();
        }
        $type = array_key_exists('type', $definition) ? $definition['type'] : 'any';
        switch ($type) {
            case 'any':
                return $this->createAnyType($actions, $nullable);
            case 'string':
                return $this->createString($definition, $actions, $nullable);
            case 'boolean':
                return $this->createBool($actions, $nullable);
            case 'integer':
                return $this->createInteger($definition, $actions, $nullable);
            case 'number':
                return $this->createNumber($definition, $actions, $nullable);
            case 'array':
                return $this->createArray($definition, $actions, $nullable);
            case 'object':
                return $this->createObject($definition, $actions, $nullable);
        }
        throw new WrongRuleDefinitionException();
    }

    private function createAnyType(array $actions, bool $nullable): Rule
    {
        $key = $nullable ? '?' : '';
        $key .= 'any/' . implode('', $actions);
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = new AnyRule($actions, $nullable);
        }
        return $this->cache[$key];
    }

    private function createString(array $definition, array $actions, bool $nullable): Rule
    {
        $enum = array_key_exists('enum', $definition) ? (array)$definition['enum'] : null;
        $min = array_key_exists('minLength', $definition) ? (int)$definition['minLength'] : 0;
        $max = array_key_exists('maxLength', $definition) ? (int)$definition['maxLength'] : 0;
        if (!$max) {
            $max = null;
        }
        $format = array_key_exists('format', $definition) ? trim($definition['format']) : '';
        $pattern = array_key_exists('pattern', $definition) ? trim($definition['pattern']) : '';
        $checker = $pattern ? $this->formats->getPatternFormat($format, $pattern) : $this->formats->getFormat($format);
        $key = $nullable ? '?' : '';
        $key .= $format ?? 'string';
        if ($enum) {
            $key .= '<' . implode('|', $enum) . '>';
        }
        $key .= '(' . $min . '-' . $max . ')';
        $key .= '/' . implode('', $actions);
        if ($pattern) {
            $key .= ':' . $pattern;
        }
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = new StringRule($checker, $actions, $min, $max, $nullable, $enum);
        }
        return $this->cache[$key];
    }

    private function createBool(array $actions, bool $nullable): Rule
    {
        $key = $nullable ? '?bool' : 'bool';
        $key .= '/' . implode('', $actions);
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = new BooleanRule($actions, $nullable);
        }
        return $this->cache[$key];
    }

    private function createInteger(array $definition, array $actions, bool $nullable): Rule
    {
        $format = array_key_exists('format', $definition) ? trim($definition['format']) : null;
        $min = array_key_exists('minimal', $definition) ? (int)$definition['minimal'] : null;
        $max = array_key_exists('maximal', $definition) ? (int)$definition['maximal'] : null;
        $exMin = array_key_exists('exclusiveMinimal', $definition) ? (bool)$definition['exclusiveMinimal'] : false;
        $exMax = array_key_exists('exclusiveMaximal', $definition) ? (bool)$definition['exclusiveMaximal'] : false;
        $multiple = array_key_exists('multipleOf', $definition) ? (int)$definition['multipleOf'] : null;
        $key = $nullable ? '?' : '';
        $key .= $format ?? 'int';
        $key .= '(' . $min . '-' . $max . ')';
        $key .= '/' . implode('', $actions);
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = new IntegerRule($actions, $min, $max, $exMin, $exMax, $multiple, $nullable, $format);
        }
        return $this->cache[$key];
    }

    private function createNumber(array $definition, array $actions, bool $nullable): Rule
    {
        $format = array_key_exists('format', $definition) ? trim($definition['format']) : null;
        $min = array_key_exists('minimal', $definition) ? (float)$definition['minimal'] : null;
        $max = array_key_exists('maximal', $definition) ? (float)$definition['maximal'] : null;
        $exMin = array_key_exists('exclusiveMinimal', $definition) ? (bool)$definition['exclusiveMinimal'] : false;
        $exMax = array_key_exists('exclusiveMaximal', $definition) ? (bool)$definition['exclusiveMaximal'] : false;
        $multiple = array_key_exists('multipleOf', $definition) ? (float)$definition['multipleOf'] : null;
        $key = $nullable ? '?' : '';
        $key .= $format ?? 'number';
        $key .= '(' . $min . '-' . $max . ')';
        $key .= '/' . implode('', $actions);
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = new NumberRule($actions, $min, $max, $exMin, $exMax, $multiple, $nullable, $format);
        }
        return $this->cache[$key];
    }

    private function createArray(array $definition, array $actions, bool $nullable): Rule
    {
        $items = array_key_exists('items', $definition) ? (array)$definition['items'] : null;
        $min = array_key_exists('minItems', $definition) ? (int)$definition['minItems'] : null;
        $max = array_key_exists('maxItems', $definition) ? (int)$definition['maxItems'] : null;
        if (!$max) {
            $max = null;
        }
        $unique = array_key_exists('uniqueItems', $definition) ? (bool)$definition['uniqueItems'] : false;
        return new ArrayRule($this, $actions, $items, $min, $max, $unique, $nullable);
    }

    private function createObject(array $definition, array $actions, bool $nullable): Rule
    {
        $properties = array_key_exists('properties', $definition) ? (array)$definition['properties'] : null;
        $required = array_key_exists('required', $definition) ? (array)$definition['required'] : null;
        $min = array_key_exists('minProperties', $definition) ? (int)$definition['minProperties'] : null;
        $max = array_key_exists('maxProperties', $definition) ? (int)$definition['maxProperties'] : null;
        if (!$max) {
            $max = null;
        }
        $extra = array_key_exists('additionalProperties', $definition) ? $definition['additionalProperties'] : null;
        if ($extra === false) {
            $extra = null;
        }
        if (!is_array($extra) && !is_null($extra)) {
            $extra = ['nullable' => true];
        }
        return new ObjectRule($this, $actions, $properties, $required, $extra, $min, $max, $nullable);
    }

    private function createOneOf(array $definition, array $actions, bool $nullable): Rule
    {
        if (!is_array($definition['oneOf'])) {
            throw new WrongRuleDefinitionException();
        }
        $rules = [];
        foreach ($definition['oneOf'] as $def) {
            if (!$def || !is_array($def)) {
                throw new WrongRuleDefinitionException();
            }
            $rules[] = $this->getRule($def);
        }
        return new MixedOneOfRule($actions, $rules, $nullable);
    }

    private function createAllOf(array $definition, array $actions, bool $nullable): Rule
    {
        if (!is_array($definition['allOf'])) {
            throw new WrongRuleDefinitionException();
        }
        $rules = [];
        foreach ($definition['allOf'] as $def) {
            if (!$def || !is_array($def)) {
                throw new WrongRuleDefinitionException();
            }
            $rules[] = $this->getRule($def);
        }
        return new MixedAllOfRule($actions, $rules, $nullable);
    }

    private function createAnyOf(array $definition, array $actions, bool $nullable): Rule
    {
        if (!is_array($definition['anyOf'])) {
            throw new WrongRuleDefinitionException();
        }
        $rules = [];
        foreach ($definition['anyOf'] as $def) {
            if (!$def || !is_array($def)) {
                throw new WrongRuleDefinitionException();
            }
            $rules[] = $this->getRule($def);
        }
        return new MixedAnyOfRule($actions, $rules, $nullable);
    }

    private function createNot(array $definition, array $actions, bool $nullable): Rule
    {
        if (!is_array($definition['not'])) {
            throw new WrongRuleDefinitionException();
        }
        $rules = [];
        foreach ($definition['not'] as $def) {
            if (!$def || !is_array($def)) {
                throw new WrongRuleDefinitionException();
            }
            $rules[] = $this->getRule($def);
        }
        return new MixedNotRule($actions, $rules, $nullable);
    }
}
