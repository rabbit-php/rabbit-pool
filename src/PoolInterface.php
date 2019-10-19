<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 18:54
 */

namespace rabbit\pool;

/**
 * Interface PoolInterface
 */
interface PoolInterface
{
    /**
     * @return ConnectionInterface
     */
    public function createConnection(): ConnectionInterface;

    /**
     * Get a connection
     *
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface;

    /**
     * Relesea the connection
     *
     * @param ConnectionInterface $connection
     */
    public function release(ConnectionInterface $connection);

    /**
     * @return string
     */
    public function getConnectionAddress(): string;

    /**
     * @return PoolConfigInterface
     */
    public function getPoolConfig(): PoolConfigInterface;

    /**
     * @return int
     */
    public function getTimeout(): int;

    /**
     * @return \Swoole\Coroutine\Channel
     */
    public function getChannelPool(): ?\Swoole\Coroutine\Channel;

    /**
     * @return null|\SplQueue
     */
    public function getQueuePool(): ?\SplQueue;
}
