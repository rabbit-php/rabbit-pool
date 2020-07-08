<?php
declare(strict_types=1);

namespace Rabbit\Pool;

/**
 * Interface IConnectionProperties
 * @package Rabbit\Pool
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
    public function setUri(string $uri): void;
}