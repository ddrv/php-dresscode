<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

final class InvalidType extends ValueError
{

    /**
     * @var string
     */
    private $type;

    public function __construct(string $path, string $type)
    {
        parent::__construct($path, 'is not a ' . $type);
        $this->type = $type;
    }

    public function getCorrectType(): string
    {
        return $this->type;
    }

    public function getToken(): string
    {
        return self::TOKEN_INVALID_TYPE;
    }

    public function getContext(): array
    {
        return [
            'correct_type' => $this->type,
        ];
    }
}
