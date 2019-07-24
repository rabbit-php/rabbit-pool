<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 19:13
 */

namespace rabbit\pool;

use rabbit\core\BaseObject;

/**
 * Class PoolProperties
 * @package rabbit\pool
 */
class PoolProperties extends BaseObject implements PoolConfigInterface
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
    protected $maxWait = 20;

    /**
     * @var \SplQueue
     */
    protected $waitStack;

    /**
     * Maximum waiting time
     *
     * @var int
     */
    protected $maxWaitTime = 3;

    /**
     * Maximum idle time
     *
     * @var int
     */
    protected $maxIdleTime = 60;

    /**
     * Connection timeout
     *
     * @var float
     */
    protected $timeout = 3;

    /**
     * Connection addresses
     * <pre>
     * [
     *  '127.0.0.1:88',
     *  '127.0.0.1:88'
     * ]
     * </pre>
     *
     * @var array
     */
    protected $uri = [];
    /**
     * @var int
     */
    protected $maxReconnect = -1;

    /**
     * Initialize
     */
    public function __construct()
    {
        if (empty($this->name)) {
            $this->name = uniqid();
        }
        $this->waitStack = new \SplQueue();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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

    public function getWaitStack(): \SplQueue
    {
        return $this->waitStack;
    }

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
        return $this->uri;
    }

    /**
     * @return int
     */
    public function getMinActive(): int
    {
        return $this->minActive;
    }

    /**
     * @return int
     */
    public function getMaxWaitTime(): int
    {
        return $this->maxWaitTime;
    }

    /**
     * @return int
     */
    public function getMaxIdleTime(): int
    {
        return $this->maxIdleTime;
    }

    public function getMaxReonnect(): int
    {
        return $this->maxReconnect;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}