<?php

declare(strict_types=1);

namespace Rabbit\Pool;


use Rabbit\Base\Exception\NotSupportedException;

/**
 * Class BaseManager
 * @package Rabbit\Pool
 */
class BaseManager
{
    protected array $items = [];

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
     * @Author Albert 63851587@qq.com
     * @DateTime 2020-10-23
     * @return array
     */
    public function getAll(): array
    {
        return $this->items;
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
     * @return mixed
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
