<?php
declare(strict_types=1);

namespace Rabbit\Pool;

use Rabbit\Base\Contract\InitInterface;

/**
 * Interface IBase
 * @package Rabbit\Pool
 */
interface IBase extends InitInterface
{
    /**
     * @return PoolInterface
     */
    public function getPool(): PoolInterface;

    /**
     * @param bool $release
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