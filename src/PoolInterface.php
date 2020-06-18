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
     * @return mixed
     */
    public function create();

    /**
     * @return mixed
     */
    public function get();

    /**
     * @param $connection
     * @return mixed
     */
    public function release($connection);

    /**
     * @return PoolConfigInterface
     */
    public function getPoolConfig(): PoolConfigInterface;

    /**
     * @return \Swoole\Coroutine\Channel
     */
    public function getPool(): ?\Swoole\Coroutine\Channel;

    /**
     * @return int
     */
    public function getCurrentCount(): int;

    /**
     * @return int
     */
    public function sub(): int;
}
