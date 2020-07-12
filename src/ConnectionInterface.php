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

    /**
     * @param bool $release
     */
    public function release($release = false): void;
}
