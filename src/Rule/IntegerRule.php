<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Rule;

use Ddrv\DressCode\Action;

final class IntegerRule extends NumericRule
{

    public function __construct(
        array $actions,
        ?int $minimal,
        ?int $maximal,
        bool $exclusiveMinimal,
        bool $exclusiveMaximal,
        ?int $multipleOf,
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
            return is_int($value);
        }
        return filter_var($value, FILTER_VALIDATE_INT) === false ? false : true;
    }

    protected function getType(): string
    {
        return 'integer';
    }

    protected function checkFormat(?string $format, $value): bool
    {
        if (!$format) {
            return true;
        }
        $value = (int)$value;
        if ($format === 'int32') {
            return ($value <= 2147483647 && $value >= -2147483648);
        }
        return true;
    }

    protected function toType($value): int
    {
        return (int)$value;
    }
}
