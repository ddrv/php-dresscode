<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;

final class NumberRule extends NumericRule
{

    public function __construct(
        array $actions,
        ?float $minimal,
        ?float $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        ?float $multipleOf,
        bool $nullable,
        ?string $format
    ) {
        parent::__construct(
            $actions,
            $minimal,
            $maximal,
            $exclusiveMinimal,
            $exclusiveMaximal,
            $multipleOf,
            $nullable,
            $format
        );
    }

    protected function checkType(bool $strictTypes, $value): bool
    {
        if ($strictTypes) {
            return is_int($value) || is_float($value);
        }
        return filter_var($value, FILTER_VALIDATE_FLOAT) === false ? false : true;
    }

    protected function getType(): string
    {
        return 'number';
    }

    protected function checkFormat(?string $format, $value): bool
    {
        return true;
    }

    protected function toType($value): float
    {
        return (float)$value;
    }
}
