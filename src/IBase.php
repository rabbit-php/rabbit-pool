<?php


namespace rabbit\pool;

use rabbit\contract\InitInterface;

/**
 * Interface ComInterface
 * @package rabbit\pool
 */
interface IBase extends InitInterface
{
    /**
     * @return ComPoolInterface
     */
    public function getPool(): PoolInterface;

    /**
     * @return void
     */
    public function release($release = false): void;

    /**
     * @return bool
     */
    public function isAutoRelease(): bool;

    /**
     * @param bool $autoRelease
     */
    public function setAutoRelease(bool $autoRelease): void;
}