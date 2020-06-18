<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 19:07
 */

namespace rabbit\pool;

use rabbit\contract\Arrayable;

/**
 * Interface PoolConfigInterface
 * @package rabbit\pool
 */
interface PoolConfigInterface extends Arrayable
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
}
