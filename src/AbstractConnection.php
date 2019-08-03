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
     * @var PoolInterface
     */
    protected $pool;

    /**
     * @var int
     */
    protected $lastTime;

    /**
     * @var string
     */
    protected $connectionId;

    /**
     * @var bool
     */
    protected $autoRelease = true;

    /**
     * Whether or not the package has been recv,default true
     *
     * @var bool
     */
    protected $recv = true;

    /**
     * AbstractConnection constructor.
     *
     * @param PoolInterface $connectPool
     */
    public function __construct(PoolInterface $connectPool)
    {
        $this->lastTime = time();
        $this->connectionId = uniqid();
        $this->pool = $connectPool;
        $this->createConnection();
    }

    /**
     * @return int
     */
    public function getLastTime(): int
    {
        return $this->lastTime;
    }

    /**
     * Update last time
     */
    public function updateLastTime(): void
    {
        $this->lastTime = time();
    }

    /**
     * @return string
     */
    public function getConnectionId(): string
    {
        return $this->connectionId;
    }

    /**
     * @return PoolInterface
     */
    public function getPool(): PoolInterface
    {
        return $this->pool;
    }

    /**
     * @return bool
     */
    public function isRecv(): bool
    {
        return $this->recv;
    }

    /**
     * @param bool $recv
     */
    public function setRecv(bool $recv): void
    {
        $this->recv = $recv;
    }

    /**
     * @param bool $release
     */
    public function release($release = false): void
    {
        if ($this->isAutoRelease() || $release) {
            $this->pool->release($this);
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

    /**
     * @param bool $defer
     * @throws NotSupportedException
     */
    public function setDefer($defer = true): bool
    {
        throw new NotSupportedException('can not call ' . __METHOD__);
    }
}