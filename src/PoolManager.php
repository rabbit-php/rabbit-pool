<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/23
 * Time: 17:17
 */

namespace rabbit\pool;

/**
 * Class PoolManager
 * @package rabbit\pool
 */
class PoolManager
{
    /**
     * @var PoolInterface[]
     */
    private static $pools = [];

    /**
     * @param ConnectionPool $pool
     */
    public static function setPool(ConnectionPool $pool): void
    {
        self::$pools[$pool->getPoolConfig()->getName()] = $pool;
    }

    /**
     * @param string $name
     * @return null|ConnectionPool
     */
    public static function getPool(string $name): ?ConnectionPool
    {
        return isset(self::$pools[$name]) ? self::$pools[$name] : null;
    }

    /**
     * @return array
     */
    public static function getPools(): array
    {
        return self::$pools;
    }

    /**
     *
     */
    public static function release(): void
    {
        foreach (self::$pools as $name => $pool) {
            if (($channel = $pool->getChannelPool()) !== null) {
                while (!$channel->isEmpty()) {
                    $channel->pop();
                }
            }
            if (($queue = $pool->getQueuePool()) !== null) {
                while ($queue->count() > 0) {
                    $queue->pop();
                }
            }
        }
    }
}