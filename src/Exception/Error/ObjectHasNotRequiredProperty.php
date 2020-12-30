<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

final class ObjectHasNotRequiredProperty extends ValueError
{

    /**
     * @var string
     */
    private $property;

    public function __construct(string $path, string $property)
    {
        parent::__construct($path, 'object has not required ' . $property . ' property');
        $this->property = $property;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getToken(): string
    {
        return self::TOKEN_OBJECT_HAS_NOT_REQUIRED_PROPERTY;
    }

    public function getContext(): array
    {
        return [
            'property' => $this->property,
        ];
    }
}
