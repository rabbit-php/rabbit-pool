<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 0:21
 */

namespace rabbit\pool;

use rabbit\core\BaseObject;
use rabbit\core\Exception;
use rabbit\helper\UrlHelper;
use Swoole\Coroutine\Channel;

/**
 * Class ConnectionPool
 * @package rabbit\pool
 */
abstract class ConnectionPool extends BaseObject implements PoolInterface
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
     * @var PoolConfigInterface
     */
    protected $poolConfig;

    /**
     * @var Channel
     */
    protected $channel;

    /**
     * @var \SplQueue
     */
    protected $queue;

    /**
     * @var bool
     */
    protected $useChannel = false;

    /**
     * Initialization
     */
    public function __construct(PoolConfigInterface $poolConfig)
    {
        $this->poolConfig = $poolConfig;
        if ($this->useChannel) {
            $this->channel = new Channel($this->poolConfig->getMaxActive());
        } else {
            $this->queue = new \SplQueue();
        }
        PoolManager::setPool($this);
    }

    public function getChannelPool(): ?\Swoole\Coroutine\Channel
    {
        return $this->channel;
    }

    public function getQueuePool(): ?\SplQueue
    {
        return $this->queue;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        if ($this->useChannel) {
            $connection = $this->getConnectionByChannel();
        } else {
            $connection = $this->getConnectionByQueue();
        }

        if ($connection->check() === false) {
            $connection->reconnect();
        }
        return $connection;
    }

    /***
     * Get connection by channel
     *
     * @return ConnectionInterface
     * @throws ConnectionException
     */
    private function getConnectionByChannel(): ConnectionInterface
    {
        if ($this->channel === null) {
            $this->channel = new Channel($this->poolConfig->getMinActive());
        }

        $stats = $this->channel->stats();
        if ($stats['queue_num'] > 0) {
            return $this->getOriginalConnection(true);
        }

        $maxActive = $this->poolConfig->getMaxActive();
        if ($this->currentCount >= $maxActive) {
            $maxWaitTime = $this->poolConfig->getMaxWaitTime();
            if ($maxWaitTime === 0) {
                return $this->channel->pop();
            }

            $result = $this->channel->pop($maxWaitTime);
            if ($result === false) {
                throw new Exception('Connection pool waiting queue timeout, timeout=' . $maxWaitTime);
            }
            return $result;
        }

        try {
            $this->currentCount++;
            $connection = $this->createConnection();
        } catch (\Throwable $exception) {
            $this->currentCount--;
            throw new Exception('Connection create failed');
        }

        return $connection;
    }

    /**
     * Get original connection
     *
     * @param bool $isChannel
     *
     * @return ConnectionInterface
     */
    private function getOriginalConnection(bool $isChannel = true): ConnectionInterface
    {
        if ($isChannel) {
            return $this->channel->pop();
        }

        return $this->queue->shift();
    }

    /**
     * Get connection by queue
     *
     * @return ConnectionInterface
     * @throws ConnectionException
     */
    private function getConnectionByQueue(): ConnectionInterface
    {
        if ($this->queue == null) {
            $this->queue = new \SplQueue();
        }
        if (!$this->queue->isEmpty()) {
            return $this->getOriginalConnection(false);
        }

        if ($this->currentCount >= $this->poolConfig->getMaxActive()) {
            if ($this->poolConfig->getMaxWait() > 0 && $this->poolConfig->getWaitStack()->count() > $this->poolConfig->getMaxWait()) {
                throw new Exception('Connection pool queue is full');
            }
            $this->poolConfig->getWaitStack()->push(\Co::getCid());
            \CO::yield();
            return $this->getOriginalConnection(false);
        }

        try {
            $this->currentCount++;
            $connect = $this->createConnection();
        } catch (\Throwable $exception) {
            $this->currentCount--;
            throw new Exception('Connection create failed');
        }


        return $connect;
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function release(ConnectionInterface $connection)
    {
        $connection->setRecv(true);
        $connection->setAutoRelease(true);

        if ($this->useChannel) {
            $this->releaseToChannel($connection);
        } else {
            $this->releaseToQueue($connection);
        }
    }

    /**
     * Release to channel
     *
     * @param $connection
     */
    private function releaseToChannel(ConnectionInterface $connection)
    {
        $stats = $this->channel->stats();
        $maxActive = $this->poolConfig->getMinActive();
        if ($stats['queue_num'] < $maxActive) {
            $this->channel->push($connection);
        } else {
            $this->currentCount--;
        }
    }

    /**
     * Release to queue
     *
     * @param $connection
     */
    private function releaseToQueue(ConnectionInterface $connection)
    {
        if ($this->queue->count() < $this->poolConfig->getMinActive()) {
            $this->queue->push($connection);
        } else {
            $this->currentCount--;
        }
        if ($this->poolConfig->getWaitStack()->count() > 0) {
            $id = $this->poolConfig->getWaitStack()->shift();
            \Swoole\Coroutine::resume($id);
        }
    }

    /**
     * @param bool $parse
     * @return string
     */
    public function getConnectionAddress(bool $parse = false): string
    {
        $serviceList = $this->getServiceList($parse);
        return $serviceList[array_rand($serviceList)];
    }

    /**
     * @return array
     */
    public function getServiceList(bool $parse = false): array
    {
        $name = $this->poolConfig->getName();
        $uris = $this->poolConfig->getUri();
        if (empty($uris)) {
            $error = sprintf('Service does not configure uri name=%s', $name);
            throw new \InvalidArgumentException($error);
        }

        if ($parse) {
            return UrlHelper::dns2IP($uris);
        }
        return $uris;
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
    public function getTimeout(): int
    {
        return $this->poolConfig->getTimeout();
    }
}
