<?php
declare(strict_types=1);

namespace rabbit\pool;

/**
 * Interface IConnectionPool
 * @package rabbit\pool
 */
interface IConnectionPool
{

    /**
     * @return int
     */
    public function getTimeout(): int;

    /**
     * @param bool $parse
     * @return string
     */
    public function getConnectionAddress(bool $parse = false): string;

    /**
     * @return array
     */
    public function getServiceList(bool $parse = false): array;
}