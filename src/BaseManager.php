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
    protected $items = [];

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
        foreach ($configs as $name => $item) {
            if (!isset($this->items[$name])) {
                $this->items[$name] = $item;
            }
        }
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name = 'default')
    {
        if (!isset($this->items[$name])) {
            return null;
        }
        return $this->items[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->items[$name]);
    }

    /**
     * @param $name
     * @param $arguments
     * @throws NotSupportedException
     */
    public function __call($name, $arguments)
    {
        $name = substr($name, 0, 3);
        if (!method_exists($this, $name)) {
            throw new NotSupportedException(__CLASS__ . " has no method $name");
        }
        return $this->$name(...$arguments);
    }
}