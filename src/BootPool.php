<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 12:08
 */

namespace rabbit\pool;


use rabbit\App;
use rabbit\server\BootInterface;

/**
 * Class BootPool
 * @package rabbit\pool
 */
class BootPool implements BootInterface
{
    public function handle(): void
    {
        $server = App::getServer();
        $server->pools = [];
    }

}