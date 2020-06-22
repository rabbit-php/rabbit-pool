<?php
declare(strict_types=1);

namespace rabbit\pool;

use rabbit\App;
use rabbit\exception\NotSupportedException;

/**
 * Class Unity
 * @package rabbit\pool
 */
class Unity
{
    /** @var BasePool */
    protected $pool;

    /**
     * Client constructor.
     * @param BasePool $pool
     */
    public function __construct(BasePool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * @param callable $function
     * @throws \rabbit\core\Exception
     */
    public function __invoke(callable $function)
    {
        $client = $this->pool->get();
        if ($client instanceof IUnity) {
            $client = $client->build();
        }
        try {
            return call_user_func($function, $client);
        } catch (Throwable $exception) {
            App::error($exception->getMessage());
            throw $exception;
        } finally {
            $this->pool->release($client);
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Throwable
     */
    public function __call($name, $arguments)
    {
        $client = $this->pool->get();
        if ($client instanceof IUnity) {
            $client = $client->build();
        }
        try {
            if (is_callable([$client, $name])) {
                return $client->$name(...$arguments);
            }
            throw new NotSupportedException(get_class($client) . " has no method $name");
        } catch (Throwable $exception) {
            App::error($exception->getMessage());
            throw $exception;
        } finally {
            $this->pool->release($client);
        }
    }
}