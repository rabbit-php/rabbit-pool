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
    public function getOptions(): array;
    /**
     * @return int
     */
    public function getMaxActive(): int;

    /**
     * @return int
     */
    public function getMaxWait(): int;

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
    public function setUri($uri):void;

    /**
     * @return int
     */
    public function getMinActive(): int;

    /**
     * @return int
     */
    public function getMaxWaitTime(): int;

    /**
     * @return int
     */
    public function getMaxReonnect():int;
}
