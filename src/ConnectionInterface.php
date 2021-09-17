<?php

declare(strict_types=1);

namespace Rabbit\Pool;

/**
 * Interface ConnectionInterface
 * @package Rabbit\Pool
 */
interface ConnectionInterface
{
    public function createConnection(): void;

    public function reconnect(): void;

    public function release(bool $release = false): void;
}
