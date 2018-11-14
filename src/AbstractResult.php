<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/17
 * Time: 17:53
 */

namespace rabbit\pool;


use rabbit\contract\ResultInterface;
use rabbit\parser\ParserInterface;

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
     * @var ParserInterface
     */
    protected $parser;

    /**
     * AbstractResult constructor.
     * @param ConnectionInterface $connection
     * @param $result
     * @param null $parser
     */
    public function __construct(ConnectionInterface $connection, $result, $parser = null)
    {
        $this->result = $result;
        $this->connection = $connection;
        $this->parser = $parser;
    }

    /**
     * @param bool $defer
     * @param bool $release
     * @return mixed
     */
    protected function recv(bool $defer = false, float $timeout = null, bool $release = true)
    {
        if ($this->connection instanceof ConnectionInterface) {
            $result = $this->connection->receive($timeout);
            $this->release($release);

            return $result;
        }

        $result = $this->connection->recv($timeout);
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