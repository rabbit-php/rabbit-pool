<?php


namespace rabbit\pool;

use rabbit\core\BaseObject;
use rabbit\core\Exception;
use rabbit\core\ObjectFactory;
use Swoole\Coroutine\Channel;

/**
 * Class BasePool
 * @package rabbit\pool
 */
class BasePool extends BaseObject implements PoolInterface
{
    /**
     * Current connection count
     *
     * @var int
     */
    protected $currentCount = 0;

    /**
     * Pool config
     *
     * @var ComPoolConfigInterface
     */
    protected $poolConfig;

    /**
     * @var Channel
     */
    protected $channel;

    /** @var string */
    protected $objclass;

    /**
     * BaseCompool constructor.
     * @param PoolConfigInterface $poolConfig
     */
    public function __construct(PoolConfigInterface $poolConfig)
    {
        $this->poolConfig = $poolConfig;
        $this->channel = new Channel($poolConfig->getMinActive());
        PoolManager::setPool($this);
    }

    public function __clone()
    {
        $this->poolConfig = clone $this->poolConfig;
        $this->channel = $this->channel ? clone $this->channel : null;
        PoolManager::setPool($this);
    }

    /**
     * @return Channel|null
     */
    public function getPool(): ?\Swoole\Coroutine\Channel
    {
        return $this->channel;
    }

    /**
     * @return mixed|ConnectionInterface
     * @throws Exception
     */
    public function get()
    {
        if (!$this->channel->isEmpty()) {
            return $this->channel->pop();
        }

        $maxActive = $this->poolConfig->getMaxActive();
        if ($maxActive > 0 && $this->currentCount >= $maxActive) {
            $maxWait = $this->poolConfig->getMaxWait();
            $result = $this->channel->pop($maxWait > 0 ? $maxWait : null);
            if ($result === false) {
                throw new Exception('Connection pool waiting queue timeout, timeout=' . $maxWait);
            }
            return $result;
        }

        try {
            $this->currentCount++;
            $connection = $this->create();
        } catch (\Throwable $exception) {
            $this->currentCount--;
            throw new Exception('Connection create failed');
        }

        return $connection;
    }

    /**
     * @return mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function create()
    {
        $config = $this->getPoolConfig()->getConfig();
        return ObjectFactory::createObject([
            'class' => $this->objclass,
            'poolKey' => $this->getPoolConfig()->getName()
        ], $config, false);
    }

    /**
     * @return int
     */
    public function getCurrentCount(): int
    {
        return $this->currentCount;
    }

    /**
     * @param $connection
     * @return mixed|void
     */
    public function release($connection)
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

    /**
     * @return PoolConfigInterface
     */
    public function getPoolConfig(): PoolConfigInterface
    {
        return $this->poolConfig;
    }

    /**
     * @return int
     */
    public function sub(): int
    {
        return $this->currentCount--;
    }
}