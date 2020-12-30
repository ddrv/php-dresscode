<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

final class NotNullableValue extends ValueError
{

    public function __construct(string $path)
    {
        parent::__construct($path, 'not nullable value');
    }

    public function getToken(): string
    {
        return self::TOKEN_NOT_NULLABLE_VALUE;
    }

    public function getContext(): array
    {
        return [];
    }
}
