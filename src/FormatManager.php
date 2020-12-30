<?php

declare(strict_types=1);

namespace Ddrv\DressCode;

use Ddrv\DressCode\Format\Format;
use Ddrv\DressCode\Format\PatternFormat;
use Ddrv\DressCode\Format\VoidFormat;

final class FormatManager
{

    /**
     * @var Format[]
     */
    private $formats = [];

    public function __construct()
    {
        $this->formats[''] = new VoidFormat();
    }

    public function registerFormat(string $name, Format $format)
    {
        $this->formats[$name] = $format;
    }

    public function getFormat(string $name): Format
    {
        if (!array_key_exists($name, $this->formats)) {
            return $this->formats[''];
        }
        return $this->formats[$name];
    }

    public function getPatternFormat(string $base, string $pattern)
    {
        $key = $base . '|' . $pattern;
        if (!array_key_exists($key, $this->formats)) {
            $this->formats[$key] = new PatternFormat($this->getFormat($base), $pattern);
        }
        return $this->formats[$key];
    }
}
