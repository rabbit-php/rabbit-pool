<?php
declare(strict_types=1);

namespace Rabbit\Pool;

/**
 * Interface PoolInterface
 * @package Rabbit\Pool
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

    public function getPool();

    /**
     * @return int
     */
    public function getCurrentCount(): int;

    /**
     * @return int
     */
    public function sub(): int;
}
