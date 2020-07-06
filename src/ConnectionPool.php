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
    /**
     * @param bool $parse
     * @return string
     */
    public function getConnectionAddress(bool $parse = false): string
    {
        $serviceList = $this->getServiceList($parse);
        return $serviceList[array_rand($serviceList)];
    }

    /**
     * @param bool $parse
     * @return array
     */
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

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->poolConfig->getTimeout();
    }
}
