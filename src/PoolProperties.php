<?php
declare(strict_types=1);

namespace Rabbit\Pool;

/**
 * Class PoolProperties
 * @package Rabbit\Pool
 */
class PoolProperties extends BasePoolProperties implements PoolConfigInterface, IConnectionProperties
{
    /** @var int */
    protected int $timeout = 3;
    /** @var array */
    protected array $uri = [];

    /**
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * @return array
     */
    public function getUri(): array
    {
        if (is_string($this->uri)) {
            $this->uri = explode(',', $this->uri);
        }
        return $this->uri;
    }

    /**
     * @param $uri
     */
    public function setUri($uri): void
    {
        $this->uri = $uri;
    }
}
