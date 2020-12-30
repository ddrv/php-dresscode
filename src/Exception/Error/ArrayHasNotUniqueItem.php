<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

final class ArrayHasNotUniqueItem extends ValueError
{

    /**
     * @var string
     */
    private $doubleOf;

    public function __construct(string $path, string $doubleOf)
    {
        parent::__construct($path, 'double of ' . $doubleOf);
        $this->doubleOf = $doubleOf;
    }

    /**
     * @return string
     */
    public function getDoubleOf(): string
    {
        return $this->doubleOf;
    }

    public function getToken(): string
    {
        return self::TOKEN_NOT_UNIQUE_ITEMS;
    }

    public function getContext(): array
    {
        return [
            'double_of' => $this->doubleOf,
        ];
    }
}
