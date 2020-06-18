<?php
declare(strict_types=1);

namespace rabbit\pool;

use rabbit\exception\NotSupportedException;

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
        $this->add($configs);
    }

    /**
     * @param array $configs
     */
    public function add(array $configs): void
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
    public function get(string $name = 'default')
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
    public function has(string $name): bool
    {
        return isset($this->connections[$name]);
    }

    /**
     * @param $name
     * @param $arguments
     * @throws NotSupportedException
     */
    public function __call($name, $arguments)
    {
        $name = str_replace(['Connection', 'connection'], '', $name);
        if (method_exists($this, $name)) {
            $this->$name(...$arguments);
        }
        throw new NotSupportedException(__CLASS__ . " has no method $name");
    }
}