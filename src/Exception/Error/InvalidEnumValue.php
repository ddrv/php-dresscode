<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Exception\Error;

final class InvalidEnumValue extends ValueError
{

    /**
     * @var string[]
     */
    private $values;

    public function __construct(string $path, array $values)
    {
        $this->values = $values;
        $last = array_pop($values);
        if ($values) {
            $list = implode(', ', $values) . ' or ' . $last;
        } else {
            $list = $last;
        }
        parent::__construct($path, 'value must be ' . $list);
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getToken(): string
    {
        return self::TOKEN_INVALID_ENUM_VALUE;
    }

    public function getContext(): array
    {
        return [
            'values' => $this->values,
        ];
    }
}
