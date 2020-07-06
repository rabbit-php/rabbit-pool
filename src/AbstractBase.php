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
    /**
     * @var string
     */
    protected string $poolKey;
    /**
     * @var bool
     */
    protected bool $autoRelease = true;

    /**
     * AbstractCom constructor.
     * @param string $poolKey
     */
    public function __construct(string $poolKey)
    {
        $this->poolKey = $poolKey;
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
