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
    /** @var string  */
    protected string $name = '';

    /**
     * Minimum active number of connections
     *
     * @var int
     */
    protected int $minActive = 5;

    /**
     * Maximum active number of connections
     *
     * @var int
     */
    protected int $maxActive = 10;

    /**
     * Maximum waiting for the number of connections, if there is no limit to 0
     *
     * @var int
     */
    protected int $maxWait = 3;

    /** @var array */
    protected array $config = [];

    /**
     * @var int
     */
    protected int $maxRetry = 3;

    /**
     * Initialize
     */
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @return int
     */
    public function getMaxActive(): int
    {
        return $this->maxActive;
    }

    /**
     * @return int
     */
    public function getMaxWait(): int
    {
        return $this->maxWait;
    }

    /**
     * @return int
     */
    public function getMinActive(): int
    {
        return $this->minActive;
    }

    public function getMaxRetry(): int
    {
        return $this->maxRetry;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return \get_object_vars($this);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
