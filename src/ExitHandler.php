<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/23
 * Time: 17:29
 */

namespace rabbit\pool;


use rabbit\server\WorkExitInterface;

/**
 * Class ExitHandler
 * @package rabbit\pool
 */
class ExitHandler implements WorkExitInterface
{
    public function handle(int $worker_id): void
    {
        PoolManager::release();
    }

}