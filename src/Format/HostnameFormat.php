<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Format;

use Ddrv\DressCode\Exception\WrongFormatException;
use Ddrv\DressCode\Tool\PunycodeEncoder;

final class HostnameFormat extends Format
{

    /**
     * @var PunycodeEncoder|null
     */
    private $encoder;

    /**
     * @inheritDoc
     */
    public function check(string $value): void
    {
        if ($this->checkValue($value)) {
            return;
        }
        if ($this->checkValue($this->punycode($value))) {
            return;
        }
        throw new WrongFormatException('hostname');
    }

    private function checkValue(?string $value): bool
    {
        if (is_null($value)) {
            return false;
        }
        return filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false ? false : true;
    }

    private function punycode(string $value): ?string
    {
        return $this->getPunycodeEncoder()->encode($value);
    }

    private function getPunycodeEncoder(): PunycodeEncoder
    {
        if (!$this->encoder) {
            $this->encoder = new PunycodeEncoder();
        }
        return $this->encoder;
    }
}
