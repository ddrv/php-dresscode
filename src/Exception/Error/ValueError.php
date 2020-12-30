<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

abstract class ValueError
{

    public const TOKEN_INVALID_TYPE                     = 'invalid_type';
    public const TOKEN_NOT_UNIQUE_ITEMS                 = 'not_unique_items';
    public const TOKEN_INVALID_ARRAY_SIZE               = 'invalid_array_size';
    public const TOKEN_INVALID_OBJECT_SIZE              = 'invalid_object_size';
    public const TOKEN_INVALID_STRING_SIZE              = 'invalid_string_size';
    public const TOKEN_INVALID_ENUM_VALUE               = 'invalid_enum_value';
    public const TOKEN_INVALID_FORMAT                   = 'invalid_format';
    public const TOKEN_VALUE_OUT_OF_LIMITS              = 'value_out_of_limits';
    public const TOKEN_VALUE_NOT_MULTIPLE_OF            = 'value_not_multiple_of';
    public const TOKEN_NOT_NULLABLE_VALUE               = 'not_nullable_value';
    public const TOKEN_OBJECT_HAS_NOT_REQUIRED_PROPERTY = 'object_has_not_required_property';

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $message;

    public function __construct(string $path, string $message)
    {
        $this->path = $path;
        $this->message = $message;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    abstract public function getToken(): string;

    abstract public function getContext(): array;
}
