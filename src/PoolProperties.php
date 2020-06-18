<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 19:13
 */

namespace rabbit\pool;

/**
 * Class PoolProperties
 * @package rabbit\pool
 */
class PoolProperties extends BasePoolProperties implements PoolConfigInterface, IConnectionProperties
{
    /** @var int */
    protected $timeout = 3;
    /** @var array */
    protected $uri = [];

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
