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
use rabbit\helper\CoroHelper;
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
            $this->channel = new Channel($this->poolConfig->getMaxActive());
        }

        $stats = $this->channel->stats();
        if ($stats['queue_num'] > 0) {
            return $this->getEffectiveConnection($stats['queue_num']);
        }

        $maxActive = $this->poolConfig->getMaxActive();
        if ($this->currentCount < $maxActive) {
            $connection = $this->createConnection();
            $this->currentCount++;

            return $connection;
        }

        $maxWait = $this->poolConfig->getMaxWait();
        if ($maxWait != 0 && $stats['consumer_num'] >= $maxWait) {
            throw new Exception(sprintf('Connection pool waiting queue is full, maxActive=%d,maxWait=%d,currentCount=%d', $maxActive, $maxWait, $this->currentCount));
        }

        $maxWaitTime = $this->poolConfig->getMaxWaitTime();
        if ($maxWaitTime == 0) {
            return $this->channel->pop();
        }

        $result = $this->channel->pop($maxWaitTime);
        if ($result === false) {
            throw new Exception('Connection pool waiting queue timeout, timeout=' . $maxWaitTime);
        }
        return $result;

        $writes = [];
        $reads = [$this->channel];
        $result = $this->channel->select($reads, $writes, $maxWaitTime);

        if ($result === false || empty($reads)) {
            throw new Exception('Connection pool waiting queue timeout, timeout=' . $maxWaitTime);
        }

        $readChannel = $reads[0];

        return $readChannel->pop();
    }

    /**
     * Get effective connection
     *
     * @param int $queueNum
     * @param bool $isChannel
     *
     * @return ConnectionInterface
     */
    private function getEffectiveConnection(int $queueNum, bool $isChannel = true): ConnectionInterface
    {
        $minActive = $this->poolConfig->getMinActive();
        if ($queueNum <= $minActive) {
            return $this->getOriginalConnection($isChannel);
        }

        $time = time();
        $moreActive = $queueNum - $minActive;
        $maxWaitTime = $this->poolConfig->getMaxWaitTime();
        for ($i = 0; $i < $moreActive; $i++) {
            /* @var ConnectionInterface $connection */
            $connection = $this->getOriginalConnection($isChannel);;
            $lastTime = $connection->getLastTime();
            if ($maxWaitTime === 0 || $time - $lastTime < $maxWaitTime) {
                return $connection;
            }
            $this->currentCount--;
        }

        return $this->getOriginalConnection($isChannel);
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
            return $this->getEffectiveConnection($this->queue->count(), false);
        }

        if ($this->currentCount >= $this->poolConfig->getMaxActive()) {
            if ($this->poolConfig->getMaxWait() > 0 && $this->poolConfig->getWaitStack()->count() > $this->poolConfig->getMaxWait()) {
                throw new Exception('Connection pool queue is full');
            }
            $this->poolConfig->getWaitStack()->push(CoroHelper::getId());
            if (\Swoole\Coroutine::suspend($this->poolConfig->getName()) == false) {
                $this->poolConfig->getWaitStack()->pop();
                throw new Exception('Reach max connections! Can not pending fetch!');
            }
            return $this->getEffectiveConnection($this->queue->count(), false);
        }

        $connect = $this->createConnection();
        $this->currentCount++;

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
        $maxActive = $this->poolConfig->getMaxActive();
        if ($stats['queue_num'] < $maxActive) {
            $this->channel->push($connection);
        }
    }

    /**
     * Release to queue
     *
     * @param $connection
     */
    private function releaseToQueue(ConnectionInterface $connection)
    {
        if ($this->queue->count() < $this->poolConfig->getMaxActive()) {
            $this->queue->push($connection);
            if ($this->poolConfig->getWaitStack()->count() > 0) {
                $id = $this->poolConfig->getWaitStack()->shift();
                \Swoole\Coroutine::resume($id);
            }
        }
    }

    /**
     * @return string
     */
    public function getConnectionAddress(): string
    {
        $serviceList = $this->getServiceList();
        return current($serviceList);
    }

    protected function getServiceList()
    {
        $name = $this->poolConfig->getName();
        $uri = $this->poolConfig->getUri();
        if (empty($uri)) {
            $error = sprintf('Service does not configure uri name=%s', $name);
            throw new \InvalidArgumentException($error);
        }

        return $uri;
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