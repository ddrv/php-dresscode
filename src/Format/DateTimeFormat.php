<?php

declare(strict_types=1);

namespace Ddrv\DressCode\Format;

use DateTime;
use Ddrv\DressCode\Exception\WrongFormatException;

final class DateTimeFormat extends Format
{

    /**
     * @inheritDoc
     */
    public function check(string $value): void
    {
        $date = $this->getDateTime($value);
        $check = str_replace(' ', 'T', substr($value, 0, 19));
        if (!$date || $date->format('Y-m-d\TH:i:s') !== $check) {
            throw new WrongFormatException('date-time');
        }
    }

    private function getDateTime(string $value): ?DateTime
    {
        $formats = [
            'Y-m-d\TH:i:sP',
            'Y-m-d\TH:i:s.uP',
            'Y-m-d H:i:sP',
            'Y-m-d H:i:s.uP',
        ];
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $value);
            if ($date) {
                return $date;
            }
        }
        return null;
    }
}
