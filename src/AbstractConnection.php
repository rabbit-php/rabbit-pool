<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 19:15
 */

namespace rabbit\pool;

/**
 * Class AbstractConnection
 * @package rabbit\pool
 */
abstract class AbstractConnection extends AbstractBase implements ConnectionInterface
{
    /** @var int */
    protected $retries = 3;
    /** @var int */
    protected $retryDelay = 1;

    /**
     * AbstractConnection constructor.
     *
     * @param PoolInterface $connectPool
     */
    public function __construct(string $poolKey)
    {
        parent::__construct($poolKey);
        $this->createConnection();
    }

    /**
     * @param bool $release
     */
    public function release($release = false): void
    {
        if ($this->isAutoRelease() || $release) {
            PoolManager::getPool($this->poolKey)->release($this);
        }
    }
}
