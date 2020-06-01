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
     * @return mixed
     */
    public function getConnection();

    /**
     * @param $connection
     * @return mixed
     */
    public function release($connection);

    /**
     * @param bool $parse
     * @return string
     */
    public function getConnectionAddress(bool $parse = false): string;

    /**
     * @return array
     */
    public function getServiceList(bool $parse = false): array;

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
    public function getPool(): ?\Swoole\Coroutine\Channel;

    /**
     * @return int
     */
    public function sub(): int;
}
