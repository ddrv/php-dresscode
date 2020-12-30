<?php

declare(strict_types=1);

namespace Ddrv\DressCode;

final class Action
{

    /**
     * @var bool
     */
    private $input;

    /**
     * @var bool
     */
    private $strictTypes;

    private function __construct(bool $input, bool $strictTypes)
    {
        $this->input = $input;
        $this->strictTypes = $strictTypes;
    }

    public static function input(bool $strictTypes = false): self
    {
        return new self(true, $strictTypes);
    }

    public static function output(bool $strictTypes = false): self
    {
        return new self(false, $strictTypes);
    }

    public function isInput(): bool
    {
        return $this->input;
    }

    public function isOutput(): bool
    {
        return !$this->input;
    }

    /**
     * @return bool
     */
    public function isStrictTypes(): bool
    {
        return $this->strictTypes;
    }
}
