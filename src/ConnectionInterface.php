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
}
