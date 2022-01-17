<?php

declare(strict_types=1);

namespace Rabbit\Pool;


use Rabbit\Base\Core\BaseObject;

/**
 * Class AbstractBase
 * @package Rabbit\Pool
 */
abstract class AbstractBase extends BaseObject implements IBase
{
    protected bool $autoRelease = true;

    public function __construct(protected string $poolKey)
    {
    }

    public function init(): void
    {
    }

    /**
     * @return PoolInterface
     */
    public function getPool(): PoolInterface
    {
        return PoolManager::getPool($this->poolKey);
    }

    public function release($release = false): void
    {
        PoolManager::getPool($this->poolKey)->release($this);
    }

    /**
     * @return bool
     */
    public function isAutoRelease(): bool
    {
        return $this->autoRelease;
    }

    /**
     * @param bool $autoRelease
     */
    public function setAutoRelease(bool $autoRelease): void
    {
        $this->autoRelease = $autoRelease;
    }
}
