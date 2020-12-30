<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode\Issues;

final class Issue1Test extends IssueTestCase
{

    protected function getRuleJson(): string
    {
        return <<<JSON
{
    "type": "object",
    "properties": {
        "email": {
            "type": "string",
            "format": "email",
            "nullable": false
        },
        "login": {
            "type": "string",
            "pattern": "^[a-z0-9\\\\.]{4,32}$",
            "nullable": false
        },
        "password": {
            "type": "string",
            "minLength": 6,
            "nullable": false
        },
        "created_at": {
            "type": "string",
            "format": "date-time",
            "readOnly": true,
            "nullable": false
        }
    },
    "required": [
        "email",
        "login",
        "password",
        "created_at"
    ],
    "additionalProperties": {}
}
JSON;
    }

    protected function getValueJson(): string
    {
        return <<<JSON
{
    "email": "ddrv@localhost",
    "login": "user",
    "password": "\$up3r-5ecr3t",
    "other": 42
}
JSON;
    }

    protected function getExpectedErrors(): ?array
    {
        return null;
    }
}
