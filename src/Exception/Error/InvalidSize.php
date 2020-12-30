<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

abstract class InvalidSize extends ValueError
{

    /**
     * @var int|null
     */
    private $minimal;

    /**
     * @var int|null
     */
    private $maximal;

    /**
     * @var bool
     */
    private $minimalIncluded;

    /**
     * @var bool
     */
    private $maximalIncluded;

    public function __construct(string $path, ?float $minimal, ?float $maximal, bool $withMinimal, bool $withMaximal)
    {
        parent::__construct($path, $this->createMessage($minimal, $maximal, $withMinimal, $withMaximal));
        $this->minimal = $minimal;
        $this->maximal = $maximal;
        $this->minimalIncluded = $withMinimal;
        $this->maximalIncluded = $withMaximal;
    }

    abstract protected function createMessage(?float $from, ?float $to, bool $withFrom, bool $withTo): string;


    public function getMaximal(): ?int
    {
        return $this->maximal;
    }

    public function setMaximal(?int $maximal): void
    {
        $this->maximal = $maximal;
    }

    public function isMinimalIncluded(): bool
    {
        return $this->minimalIncluded;
    }

    public function isMaximalIncluded(): bool
    {
        return $this->maximalIncluded;
    }

    public function getContext(): array
    {
        return [
            'minimal' => $this->minimal,
            'maximal' => $this->maximal,
            'minimalIncluded' => $this->minimalIncluded,
            'maximalIncluded' => $this->maximalIncluded,
        ];
    }
}
