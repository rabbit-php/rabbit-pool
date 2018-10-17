<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/17
 * Time: 17:53
 */

namespace rabbit\pool;


use rabbit\contract\ResultInterface;

/**
 * Class AbstractResult
 * @package rabbit\pool
 */
abstract class AbstractResult implements ResultInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * AbstractResult constructor.
     * @param ConnectionInterface $connection
     * @param $result
     */
    public function __construct(ConnectionInterface $connection, $result)
    {
        $this->result = $result;
        $this->connection = $connection;
    }

    /**
     * @param bool $defer
     * @param bool $release
     * @return mixed
     */
    protected function recv(bool $defer = false, bool $release = true)
    {
        if ($this->connection instanceof ConnectionInterface) {
            $result = $this->connection->receive();
            $this->release($release);

            return $result;
        }

        $result = $this->connection->recv();
        if ($defer) {
            $this->connection->setDefer(false);
        }

        return $result;
    }

    /**
     * @param bool $release
     */
    protected function release(bool $release = true): void
    {
        if ($this->connection instanceof ConnectionInterface && $release) {
            $this->connection->release();
        }
    }
}