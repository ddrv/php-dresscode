<?php

declare(strict_types=1);

namespace Tests\Ddrv\DressCode;

use Ddrv\DressCode\Action;
use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Exception\WrongFormatException;
use Ddrv\DressCode\Format\Format;
use Ddrv\DressCode\DressCode;
use PHPUnit\Framework\TestCase;

final class DressCodeTest extends TestCase
{

    /**
     * @throws InvalidValueException
     */
    public function testInputValidation()
    {
        $validator = $this->getValidator();
        $rules = $this->getRules();
        $data = $this->getValidData();
        $valid = $validator->validate(Action::input(), $rules, $data);
        $this->assertArrayHasKey('email', $valid);
        $this->assertArrayHasKey('login', $valid);
        $this->assertArrayHasKey('birthday', $valid);
        $this->assertArrayHasKey('password', $valid);
        $this->assertArrayNotHasKey('created_at', $valid);
    }

    /**
     * @throws InvalidValueException
     */
    public function testOutputValidation()
    {
        $validator = $this->getValidator();
        $rules = $this->getRules();
        $data = $this->getValidData();
        $valid = $validator->validate(Action::output(), $rules, $data);
        $this->assertArrayHasKey('email', $valid);
        $this->assertArrayHasKey('login', $valid);
        $this->assertArrayHasKey('birthday', $valid);
        $this->assertArrayHasKey('created_at', $valid);
        $this->assertArrayNotHasKey('password', $valid);
    }

    /**
     * @throws InvalidValueException
     */
    public function testRegisterFormat()
    {
        $validator = $this->getValidator();
        $format = new class extends Format
        {

            public function check(string $value): void
            {
                if ($value !== 'kiwi') {
                    throw new WrongFormatException('kiwi');
                }
            }
        };
        $validator->registerFormat('kiwi', $format);

        $rule = [
            'type' => 'string',
            'format' => 'kiwi',
        ];
        $valid = $validator->validate(Action::input(), $rule, 'kiwi');
        $this->assertSame('kiwi', $valid);

        try {
            $validator->validate(Action::input(), $rule, 'no kiwi');
            $this->fail('expect exception');
        } catch (InvalidValueException $exception) {
        }
    }

    private function getRules(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'email' => [
                    '$ref' => '#/entities/email',
                ],
                'login' => [
                    '$ref' => '#/entities/login',
                ],
                'birthday' => [
                    'type' => 'string',
                    'format' => 'date',
                ],
                'created_at' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'readOnly' => true,
                ],
                'password' => [
                    'type' => 'string',
                    'minLength' => 8,
                    'maxLength' => 64,
                    'writeOnly' => true,
                ],
            ],
            'required' => ['email', 'login', 'password'],
            'additionalProperties' => true,
            'nullable' => true,
        ];
    }

    private function getValidator(): DressCode
    {
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
        return $validator;
    }

    private function getValidData(): array
    {
        return [
            'email' => 'phpunit@localhost',
            'login' => 'my-login',
            'birthday' => '1996-02-15',
            'password' => 'superSecret',
            'created_at' => '2020-12-27T15:22:14+07:00',
        ];
    }
}
