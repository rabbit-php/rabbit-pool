<?php
declare(strict_types=1);

namespace rabbit\pool;

/**
 * Class BaseManager
 * @package rabbit\pool
 */
class BaseManager
{
    protected $connections = [];

    /**
     * Manager constructor.
     * @param array $configs
     */
    public function __construct(array $configs = [])
    {
        $this->addConnection($configs);
    }

    /**
     * @param array $configs
     */
    public function addConnection(array $configs): void
    {
        foreach ($configs as $name => $connection) {
            if (!isset($this->connections[$name])) {
                $this->connections[$name] = $connection;
            }
        }
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getConnection(string $name = 'default')
    {
        if (!isset($this->connections[$name])) {
            return null;
        }
        return $this->connections[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasConnection(string $name): bool
    {
        return isset($this->connections[$name]);
    }
}