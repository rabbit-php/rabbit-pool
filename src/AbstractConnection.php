<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 19:15
 */

namespace rabbit\pool;

use rabbit\exception\NotSupportedException;

/**
 * Class AbstractConnection
 * @package rabbit\pool
 */
abstract class AbstractConnection implements ConnectionInterface
{
    /**
     * @var string
     */
    protected $poolKey;

    /**
     * @var bool
     */
    protected $autoRelease = true;
    /** @var int */
    protected $retries = 3;
    /** @var int */
    protected $retryDelay = 1;

    /**
     * AbstractConnection constructor.
     *
     * @param PoolInterface $connectPool
     */
    public function __construct(string $poolKey)
    {
        $this->poolKey = $poolKey;
        $this->createConnection();
    }

    /**
     * @return PoolInterface
     */
    public function getPool(): PoolInterface
    {
        return PoolManager::getPool($this->poolKey);
    }

    /**
     * @param bool $release
     */
    public function release($release = false): void
    {
        if ($this->isAutoRelease() || $release) {
            PoolManager::getPool($this->poolKey)->release($this);
        }
    }

    /**
     * @return bool
     */
    public function isAutoRelease(): bool
    {
        return $this->autoRelease;
    }

    /**
     * @param bool $autoRelease
     */
    public function setAutoRelease(bool $autoRelease): void
    {
        $this->autoRelease = $autoRelease;
    }
}
