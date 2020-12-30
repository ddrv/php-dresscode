<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

final class NotMultipleOf extends ValueError
{

    /**
     * @var float
     */
    private $multipleOf;

    public function __construct(string $path, float $multipleOf)
    {
        parent::__construct($path, 'value is not multiple of ' . $multipleOf);
        $this->multipleOf = $multipleOf;
    }

    public function getToken(): string
    {
        return self::TOKEN_VALUE_NOT_MULTIPLE_OF;
    }

    public function getMultipleOf(): float
    {
        return $this->multipleOf;
    }

    public function getContext(): array
    {
        return [
            'multiple_of' => $this->multipleOf,
        ];
    }
}
