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
     * @param PoolInterface $pool
     */
    public static function setPool(PoolInterface $pool): void
    {
        $name = $pool->getPoolConfig()->getName();
        if (isset(self::$pools[$name])) {
            throw new \RuntimeException("The $name already set in PoolManager");
        }
        self::$pools[$name] = $pool;
    }

    /**
     * @param string $name
     * @return null|PoolInterface
     */
    public static function getPool(string $name): ?PoolInterface
    {
        return isset(self::$pools[$name]) ? self::$pools[$name] : null;
    }

    /**
     * @return PoolInterface[]
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
