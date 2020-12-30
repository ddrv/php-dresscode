# DressCode for your data

## Install

```text
composer require ddrv/dresscode
```

## Usage

```php
<?php

use Ddrv\DressCode\Action;
use Ddrv\DressCode\DressCode;

require 'vendor/autoload.php';

$validator = new DressCode();

$validator->validate(Action::output(), ['type' => 'string'], 'it is ok');
```

## Check errors

```php
<?php

use Ddrv\DressCode\Action;
use Ddrv\DressCode\DressCode;
use Ddrv\DressCode\Exception\InvalidValueException;
require 'vendor/autoload.php';

$validator = new DressCode();


$rule = [
    'type' => 'object',
    'properties' => [
        'email' => [
            'type' => 'string',
            'format' => 'email',
        ],
        'login' => [
            'type' => 'string',
            'minLength' => 5,
            'maxLength' => 32,
            'pattern' => '^[a-z\-]+$',
        ],
        'birthday' => [
            'type' => 'string',
            'format' => 'date',
        ],
    ],
    'required' => ['email', 'login'],
    'additionalProperties' => true,
    'nullable' => true,
];
$data = [
    'email' => 'ivan',
    'login' => 'ddrv',
    'birthday' => '2020-02-30',
];

try {
    $validator->validate(Action::input(), $rule, $data);
} catch (InvalidValueException $e) {
    foreach ($e->getErrors() as $error) {
        echo $error->getPath() . ': ' . $error->getMessage() . PHP_EOL;
    }
}

/*
email: ivan is not a email
login: string size must be between 5 to 32 symbols
birthday: 2020-02-30 is not a date
*/
```

## Defining rules

```php
<?php

use Ddrv\DressCode\Action;
use Ddrv\DressCode\DressCode;

require 'vendor/autoload.php';

$validator = new DressCode();
$validator->setEntity('#/entities/email', [
    'type' => 'string',
    'format' => 'email',
]);
$validator->setEntity('#/entities/login', [
    'type' => 'string',
    'minLength' => 5,
    'maxLength' => 32,
    'pattern' => '^[a-z\-]+$',
]);
$validator->setEntity('#/entities/date', [
    'type' => 'string',
    'format' => 'date',
]);

$rule = [
    'type' => 'object',
    'properties' => [
        'email' => [
            '$ref' => '#/entities/email',
        ],
        'login' => [
            '$ref' => '#/entities/login',
        ],
        'birthday' => [
            '$ref' => '#/entities/date',
        ],
        'password' => [
            'type' => 'string',
            'minLength' => 8,
            'maxLength' => 32,
            'writeOnly' => true,
        ],
    ],
    'required' => ['email', 'login'],
    'additionalProperties' => true,
    'nullable' => true,
];
$data = [
    'email' => 'ivan@ddrv.ru',
    'login' => 'i-dudarev',
    'password' => 'short',
];

$valid = $validator->validate(Action::input(), $rule, $data); // Error password
$valid = $validator->validate(Action::output(), $rule, $data); // No error because password writeOnly
$valid = $validator->validate(Action::input(), ['$ref' => '#/entities/date'], '2020-12-30');
```

## Register your string formats

```php
<?php

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\WrongFormatException;
use Ddrv\DressCode\Format\Format;
use Ddrv\DressCode\DressCode;

require 'vendor/autoload.php';

$validator = new DressCode();

$myEmailFormat = new class extends Format
{
    public function check(string $value) : void{
        if (!filter_var($value, FILTER_VALIDATE_EMAIL) !== false) {
            throw new WrongFormatException('email');
        }
    }
};
$validator->registerFormat('email', $myEmailFormat);

$rule = [
    'type' => 'string',
    'format' => 'email',
];
$validator->validate(Action::input(), $rule, 'ivan@ddrv.ru');
```

## Strict types

use `Action::input(true)` and `Action::output(true)` for strict type checking.

> **Warning**
> 
> do not use it if you are checking data `application/x-www-form-urlencoded`

## Supported types

- [x] boolean
- [x] number
- [x] number (format `float`)
- [x] number (format `double`)
- [x] integer
- [x] integer (format `int32`)
- [x] integer (format `int64`)
- [x] string
- [x] string (format `binary`)
- [x] string (format `byte`)
- [x] string (format `date`)
- [x] string (format `date-time`)
- [x] string (format `email`)
- [x] string (format `hostname`)
- [x] string (format `ip`)
- [x] string (format `ipv4`)
- [x] string (format `ipv6`)
- [x] string (format `uri`)
- [x] string (format `uuid`)
- [x] array
- [x] object

## Supported keywords

### All types

- [x] nullable
- [x] readOnly
- [x] writeOnly
- [x] default (only for object properties)

### Integer and number types

- [x] minimum
- [x] maximum
- [x] exclusiveMinimum
- [x] exclusiveMaximum
- [x] multipleOf

### String type

- [x] pattern
- [x] minLength
- [x] maxLength
- [x] enum

### Array type

- [x] items
- [x] minItems
- [x] maxItems
- [x] uniqueItems

### Object type

- [x] properties
- [x] required
- [x] additionalProperties
- [x] minProperties
- [x] maxProperties

## Support references

- [x] $ref (only after call `setEntity()` method)

## Supported polymorphism

- [x] oneOf
- [x] anyOf
- [x] allOf
- [x] not

