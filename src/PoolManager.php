<?php
declare(strict_types=1);

namespace Rabbit\Pool;

/**
 * Class PoolManager
 * @package Rabbit\Pool
 */
class PoolManager
{
    /**
     * @var PoolInterface[]
     */
    private static array $pools = [];

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
            if (($channel = $pool->getPool()) !== null) {
                while (!$channel->isEmpty()) {
                    $conn = $channel->pop();
                    if (method_exists($conn, 'close')) {
                        $conn->close();
                    }
                    unset($conn);
                }
            }
        }
    }
}
