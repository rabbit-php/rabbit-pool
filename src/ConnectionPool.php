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
     * Initialization
     */
    public function __construct(PoolConfigInterface $poolConfig)
    {
        $this->poolConfig = $poolConfig;
        $this->channel = new Channel($poolConfig->getMaxActive());
        PoolManager::setPool($this);
    }

    public function getPool(): ?\Swoole\Coroutine\Channel
    {
        return $this->channel;
    }

    /**
     * @return mixed|ConnectionInterface
     * @throws Exception
     */
    public function getConnection()
    {
        if (!$this->channel->isEmpty()) {
            return $this->channel->pop();
        }

        $maxActive = $this->poolConfig->getMaxActive();
        if ($this->currentCount >= $maxActive) {
            $maxWait = $this->poolConfig->getMaxWait();
            $result = $this->channel->pop($maxWait > 0 ? $maxWait : null);
            if ($result === false) {
                throw new Exception('Connection pool waiting queue timeout, timeout=' . $maxWait);
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
