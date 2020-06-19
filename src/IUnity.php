<?php
declare(strict_types=1);

namespace rabbit\pool;

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