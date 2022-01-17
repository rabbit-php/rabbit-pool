<?php

declare(strict_types=1);

namespace Rabbit\Pool;

/**
 * Class AbstractConnection
 * @package Rabbit\Pool
 */
abstract class AbstractConnection extends AbstractBase implements ConnectionInterface
{
    /** @var int */
    protected int $retries = 3;
    /** @var int */
    protected int $retryDelay = 1;

    public function __construct(protected string $poolKey)
    {
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
