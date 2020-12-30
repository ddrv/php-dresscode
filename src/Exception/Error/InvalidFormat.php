<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

final class InvalidFormat extends ValueError
{

    /**
     * @var string
     */
    private $format;

    /**
     * @var string|null
     */
    private $pattern;

    public function __construct(string $path, string $message, string $format, ?string $pattern)
    {
        parent::__construct($path, $message);
        $this->format = $format;
        $this->pattern = $pattern;
    }

    public function getToken(): string
    {
        return self::TOKEN_INVALID_FORMAT;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function getContext(): array
    {
        return [
            'format' => $this->format,
            'pattern' => $this->pattern,
        ];
    }
}
