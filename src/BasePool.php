<?php

declare(strict_types=1);

namespace Rabbit\Pool;

use Throwable;
use Rabbit\Base\Core\Exception;
use Rabbit\Base\Core\BaseObject;
use Rabbit\Base\Core\Channel;

/**
 * Class BasePool
 * @package Rabbit\Pool
 */
class BasePool extends BaseObject implements PoolInterface
{
    protected int $currentCount = 0;
    protected PoolConfigInterface $poolConfig;
    protected Channel $channel;
    protected string $objClass;

    /**
     * BasePool constructor.
     * @param PoolConfigInterface $poolConfig
     */
    public function __construct(PoolConfigInterface $poolConfig)
    {
        $this->poolConfig = $poolConfig;
        PoolManager::setPool($this);
    }

    public function __clone()
    {
        $this->poolConfig = clone $this->poolConfig;
        $this->currentCount = 0;
        $this->channel = new Channel($this->poolConfig->getMinActive());
    }

    public function getPool(): Channel
    {
        return $this->channel ??= new Channel($this->poolConfig->getMinActive());
    }

    public function get(): object
    {
        $this->getPool();
        if (!$this->channel->isEmpty()) {
            return $this->channel->pop();
        }

        $maxActive = $this->poolConfig->getMaxActive();
        if ($maxActive > 0 && $this->currentCount >= $maxActive) {
            $maxWait = $this->poolConfig->getMaxWait();
            if (false === $result = $this->channel->pop($maxWait > 0 ? $maxWait : -1)) {
                throw new Exception('Pool waiting queue timeout, timeout=' . $maxWait);
            }
            return $result;
        }

        try {
            $this->currentCount++;
            $connection = $this->create();
        } catch (Throwable $exception) {
            $this->currentCount--;
            throw $exception;
        }

        return $connection;
    }

    public function create(): object
    {
        $config = $this->getPoolConfig()->getConfig();
        return create([
            'class' => $this->objClass,
            'poolKey' => $this->getPoolConfig()->getName()
        ], $config, false);
    }

    public function getCurrentCount(): int
    {
        return $this->currentCount;
    }

    public function release(object $connection): void
    {
        if (!$this->channel->isFull()) {
            $this->channel->push($connection);
        } else {
            if (method_exists($connection, 'close')) {
                $connection->close();
            }
            unset($connection);
            $this->currentCount--;
        }
    }

    public function getPoolConfig(): PoolConfigInterface
    {
        return $this->poolConfig;
    }

    public function sub(): int
    {
        return $this->currentCount--;
    }
}
