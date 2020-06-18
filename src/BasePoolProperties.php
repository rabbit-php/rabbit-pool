<?php
declare(strict_types=1);

namespace rabbit\pool;


use rabbit\core\BaseObject;

/**
 * Class BasePoolProperties
 * @package rabbit\pool
 */
class BasePoolProperties extends BaseObject implements PoolConfigInterface
{
    /**
     * Pool name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Minimum active number of connections
     *
     * @var int
     */
    protected $minActive = 5;

    /**
     * Maximum active number of connections
     *
     * @var int
     */
    protected $maxActive = 10;

    /**
     * Maximum waiting for the number of connections, if there is no limit to 0
     *
     * @var int
     */
    protected $maxWait = 3;

    /** @var array */
    protected $config = [];

    /**
     * @var int
     */
    protected $maxRetry = 3;

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
}