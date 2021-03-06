<?php
declare(strict_types=1);

namespace Rabbit\Pool;

/**
 * Interface IConnectionPool
 * @package Rabbit\Pool
 */
interface IConnectionPool
{

    /**
     * @return float
     */
    public function getTimeout(): float;

    /**
     * @param bool $parse
     * @return string
     */
    public function getConnectionAddress(bool $parse = false): string;

    /**
     * @param bool $parse
     * @return array
     */
    public function getServiceList(bool $parse = false): array;
}