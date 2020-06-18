<?php


namespace rabbit\pool;

use rabbit\core\BaseObject;

/**
 * Class AbstractCom
 * @package rabbit\compool
 */
abstract class AbstractBase extends BaseObject implements IBase
{
    /**
     * @var string
     */
    protected $poolKey;
    /**
     * @var bool
     */
    protected $autoRelease = true;

    /**
     * AbstractCom constructor.
     * @param string $poolKey
     */
    public function __construct(string $poolKey = null)
    {
        $this->poolKey = $poolKey;
    }

    public function init(): void
    {

    }

    /**
     * @return ComPoolInterface
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