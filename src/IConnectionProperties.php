<?php
declare(strict_types=1);

namespace rabbit\pool;

/**
 * Interface IConnectionProperties
 * @package rabbit\pool
 */
interface IConnectionProperties
{
    /**
     * @return float
     */
    public function getTimeout(): float;

    /**
     * @return array
     */
    public function getUri(): array;

    /**
     * @param $uri
     */
    public function setUri($uri): void;
}