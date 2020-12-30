<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception;

use Exception;

final class WrongFormatException extends Exception
{

    /**
     * @var string
     */
    private $format;

    public function __construct(string $format)
    {
        $this->format = $format;
        parent::__construct('invalid value', 1);
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
