<?php

declare(strict_types=1);

namespace Ddrv\DressCode;

use Ddrv\DressCode\Exception\InvalidValueException;
use Ddrv\DressCode\Format\DateFormat;
use Ddrv\DressCode\Format\DateTimeFormat;
use Ddrv\DressCode\Format\EmailFormat;
use Ddrv\DressCode\Format\Format;
use Ddrv\DressCode\Format\HostnameFormat;
use Ddrv\DressCode\Format\IpFormat;
use Ddrv\DressCode\Format\UriFormat;
use Ddrv\DressCode\Format\UuidFormat;

final class DressCode
{

    private $formatManager;

    private $ruleManager;

    public function __construct()
    {
        $this->formatManager = new FormatManager();
        $this->formatManager->registerFormat('date', new DateFormat());
        $this->formatManager->registerFormat('date-time', new DateTimeFormat());
        $this->formatManager->registerFormat('email', new EmailFormat());
        $this->formatManager->registerFormat('uuid', new UuidFormat());
        $this->formatManager->registerFormat('uri', new UriFormat());
        $this->formatManager->registerFormat('hostname', new HostnameFormat());
        $this->formatManager->registerFormat('ip', IpFormat::all());
        $this->formatManager->registerFormat('ipv4', IpFormat::ipv4());
        $this->formatManager->registerFormat('ipv6', IpFormat::ipv6());
        $this->ruleManager = new RuleManager($this->formatManager);
    }

    public function registerFormat(string $name, Format $format)
    {
        $this->formatManager->registerFormat($name, $format);
    }

    public function setEntity(string $name, array $rule): self
    {
        $this->ruleManager->setEntity($name, $rule);
        return $this;
    }

    /**
     * @param Action $action
     * @param array $rule
     * @param mixed $value
     * @return mixed|null
     * @throws InvalidValueException
     */
    public function validate(Action $action, array $rule, $value)
    {
        return $this->ruleManager->getRule($rule)->validate($action, '', $value)[0];
    }
}
