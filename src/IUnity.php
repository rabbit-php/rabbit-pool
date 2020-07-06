<?php
declare(strict_types=1);

namespace Rabbit\Pool;

/**
 * Interface IUnity
 * @package rabbit\pool
 */
interface IUnity
{
    /**
     * @return mixed
     */
    public function build();
}