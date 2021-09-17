<?php

declare(strict_types=1);

namespace Rabbit\Pool;


use Rabbit\Base\Core\BaseObject;

/**
 * Class BasePoolProperties
 * @package Rabbit\Pool
 */
class BasePoolProperties extends BaseObject implements PoolConfigInterface
{
    protected string $name = '';

    protected int $minActive = 5;

    protected int $maxActive = 10;

    protected int $maxWait = 3;

    protected array $config = [];

    protected int $maxRetry = 3;

    public function __construct()
    {
        if (empty($this->name)) {
            $this->name = uniqid();
        }
    }

    public function __clone()
    {
        $this->name = uniqid();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getMaxActive(): int
    {
        return $this->maxActive;
    }

    public function getMaxWait(): int
    {
        return $this->maxWait;
    }

    public function getMinActive(): int
    {
        return $this->minActive;
    }

    public function getMaxRetry(): int
    {
        return $this->maxRetry;
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
