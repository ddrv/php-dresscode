<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Countable;
use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Exception\Error\InvalidArraySize;
use Ddrv\DressCode\Exception\Error\InvalidType;
use Ddrv\DressCode\Exception\Error\ArrayHasNotUniqueItem;
use Ddrv\DressCode\Rule;
use Ddrv\DressCode\RuleManager;

final class ArrayRule extends Rule
{

    /**
     * @var RuleManager
     */
    private $ruleManager;

    /**
     * @var array|null
     */
    private $items;

    /**
     * @var int|null
     */
    private $minItems;

    /**
     * @var int|null
     */
    private $maxItems;

    /**
     * @var bool
     */
    private $uniqueItems;

    public function __construct(
        RuleManager $ruleManager,
        array $actions,
        ?array $items,
        ?int $minItems,
        ?int $maxItems,
        bool $uniqueItems,
        bool $nullable
    ) {
        parent::__construct($actions, $nullable);
        $this->ruleManager = $ruleManager;
        $this->items = $items;
        $this->minItems = $minItems;
        $this->maxItems = $maxItems;
        $this->uniqueItems = $uniqueItems;
    }

    /**
     * @inheritDoc
     */
    protected function check(Action $action, string $path, $value): array
    {
        if (!is_array($value) && !$value instanceof Countable) {
            throw new InvalidValueException(new InvalidType($path, 'array'));
        }
        $result = [];
        $size = count($value);
        $errors = [];
        if (($this->minItems && $size < $this->minItems) || ($this->maxItems && $size > $this->maxItems)) {
            $errors[] = new InvalidArraySize($path, $this->minItems, $this->maxItems);
        }
        $unique = [];
        if ($this->items) {
            $rule = $this->ruleManager->getRule($this->items);
            $num = -1;
            foreach ($value as $item) {
                $num++;
                $elementPath = $path ? $path . '.' . $num : (string)$num;
                if ($this->uniqueItems) {
                    $key = md5(json_encode($item));
                    if (array_key_exists($key, $unique)) {
                        $errors[] = new ArrayHasNotUniqueItem($elementPath, $unique[$key]);
                        continue;
                    }
                    $unique[$key] = $elementPath;
                }
                try {
                    [$valid, $add] = $rule->validate($action, $elementPath, $item);
                    if ($add) {
                        $result[] = $valid;
                    }
                } catch (InvalidValueException $e) {
                    array_splice($errors, count($errors), 0, $e->getErrors());
                }
            }
            $this->throw($errors);
            return $result;
        }
        $num = -1;
        foreach ($value as $item) {
            $num++;
            $elementPath = $path ? $path . '.' . $num : (string)$num;
            $key = md5(json_encode($item));
            if (array_key_exists($key, $unique)) {
                $errors[] = new ArrayHasNotUniqueItem($elementPath, $unique[$key]);
                continue;
            }
            if ($this->uniqueItems) {
                $unique[$key] = $elementPath;
            }
            $result[] = $item;
        }
        $this->throw($errors);
        return $result;
    }
}
