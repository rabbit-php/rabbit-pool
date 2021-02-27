<?php
declare(strict_types=1);

namespace Rabbit\Pool;

use Rabbit\Base\Contract\ArrayAble;

/**
 * Interface PoolConfigInterface
 * @package Rabbit\Pool
 */
interface PoolConfigInterface extends ArrayAble
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return array
     */
    public function getConfig(): array;

    /**
     * @param array $config
     */
    public function setConfig(array $config): void;

    /**
     * @return int
     */
    public function getMaxActive(): int;

    /**
     * @return int
     */
    public function getMaxWait(): int;

    /**
     * @return int
     */
    public function getMinActive(): int;

    /**
     * @return int
     */
    public function getMaxRetry(): int;

    public function setName(string $name): void;
}
