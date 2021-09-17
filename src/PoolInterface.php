<?php

declare(strict_types=1);

namespace Rabbit\Pool;

use Rabbit\Base\Core\Channel;

/**
 * Interface PoolInterface
 * @package Rabbit\Pool
 */
interface PoolInterface
{
    public function create(): object;

    public function get(): object;

    public function release(object $connection): void;

    public function getPoolConfig(): PoolConfigInterface;

    public function getPool(): Channel;

    public function getCurrentCount(): int;

    public function sub(): int;
}
