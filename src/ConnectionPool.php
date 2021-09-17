<?php

declare(strict_types=1);

namespace Rabbit\Pool;

use Rabbit\Base\Helper\UrlHelper;

/**
 * Class ConnectionPool
 * @package Rabbit\Pool
 */
abstract class ConnectionPool extends BasePool implements PoolInterface, IConnectionPool
{
    public function getConnectionAddress(bool $parse = false): string
    {
        $serviceList = $this->getServiceList($parse);
        return $serviceList[array_rand($serviceList)];
    }

    public function getServiceList(bool $parse = false): array
    {
        $name = $this->poolConfig->getName();
        $uris = $this->poolConfig->getUri();
        if (empty($uris)) {
            $error = sprintf('Service does not configure uri name=%s', $name);
            throw new \InvalidArgumentException($error);
        }

        if ($parse) {
            return UrlHelper::dns2IP($uris);
        }
        return $uris;
    }

    public function getTimeout(): float
    {
        return $this->poolConfig->getTimeout();
    }
}
