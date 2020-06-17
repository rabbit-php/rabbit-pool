<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 19:11
 */

namespace rabbit\pool;

/**
 * Interface ConnectionInterface
 * @package rabbit\pool
 */
interface ConnectionInterface
{
    /**
     * Create connectioin
     *
     * @return void
     */
    public function createConnection(): void;

    /**
     * Reconnect
     */
    public function reconnect(): void;

    /**
     * @return PoolInterface
     */
    public function getPool(): PoolInterface;

    /**
     * @return bool
     */
    public function isAutoRelease(): bool;

    /**
     * @param bool $autoRelease
     */
    public function setAutoRelease(bool $autoRelease): void;

    /**
     * @return void
     */
    public function release($release = false): void;
}
