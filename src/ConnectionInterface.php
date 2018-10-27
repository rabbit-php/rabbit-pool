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
     * @return bool
     */
    public function check(): bool;

    /**
     * @return int
     */
    public function getLastTime(): int;

    /**
     * @return void
     */
    public function updateLastTime(): void;

    /**
     * @return string
     */
    public function getConnectionId(): string;

    /**
     * @return PoolInterface
     */
    public function getPool(): PoolInterface;

    /**
     * @return bool
     */
    public function isAutoRelease(): bool;

    /**
     * @return bool
     */
    public function isRecv(): bool;

    /**
     * @param bool $autoRelease
     */
    public function setAutoRelease(bool $autoRelease): void;

    /**
     * @param bool $recv
     */
    public function setRecv(bool $recv): void;

    /**
     * @return mixed
     */
    public function receive(float $timeout = null);

    /**
     * @param bool $defer
     */
    public function setDefer($defer = true): bool;

    /**
     * @return void
     */
    public function release($release = false): void;

}