<?php

declare(strict_types=1);

namespace Rabbit\Pool;

class PoolProperties extends BasePoolProperties implements PoolConfigInterface, IConnectionProperties
{
    protected int $timeout = 3;

    protected ?string $uri = null;

    /**
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * @return array
     */
    public function getUri(): array
    {
        return explode(',', $this->uri);
    }

    /**
     * @param $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }
}
