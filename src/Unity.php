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
        return $this->realCall($function);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Throwable
     */
    public function __call($name, $arguments)
    {
        return $this->realCall($name, $arguments);
    }

    /**
     * @param $call
     * @param array $params
     * @return mixed
     * @throws NotSupportedException
     * @throws \rabbit\core\Exception
     */
    protected function realCall($call, array $params = [])
    {
        $retrys = $this->pool->getPoolConfig()->getMaxRetry();
        $retrys = $retrys > 0 ? $retrys : 1;
        while ($retrys--) {
            $client = $this->pool->get();
            if ($client instanceof IUnity) {
                $client = $client->build();
            }
            try {
                if (is_string($call) && is_callable([$client, $call])) {
                    $result = $client->$call(...$params);
                } elseif (is_callable($call)) {
                    $result = call_user_func($call, $client);
                } else {
                    throw new NotSupportedException(get_class($client) . " has no method $name");
                }
                $this->pool->release($client);
                return $result;
            } catch (Throwable $exception) {
                $this->pool->sub();
                if ($retrys === 0) {
                    App::error($exception->getMessage());
                    throw $exception;
                }
                App::warning($exception->getMessage() . ' & retry!');
            }
        }
    }
}