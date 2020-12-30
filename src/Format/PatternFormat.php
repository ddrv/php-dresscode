<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Format;

use Ddrv\DressCode\Exception\WrongFormatException;

final class PatternFormat extends Format
{

    /**
     * @var Format
     */
    private $base;

    /**
     * @var string
     */
    private $pattern;

    public function __construct(Format $base, string $pattern)
    {
        $this->pattern = $pattern;
        $this->base = $base;
    }

    /**
     * @inheritDoc
     */
    public function check(string $value): void
    {
        $this->base->check($value);
        if (!preg_match('/' . $this->pattern . '/u', $value)) {
            throw new WrongFormatException('pattern: ' . $this->pattern);
        }
    }
}
