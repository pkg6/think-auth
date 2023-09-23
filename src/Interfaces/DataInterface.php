<?php

namespace tp5er\think\auth\Interfaces;

/**
 * Interface DataInterface
 * @package tp5er\think\auth\Interfaces
 */
interface DataInterface
{
    /**
     * @return array
     */
    public function toArray();

    /**
     * @return string
     */
    public function toJson();
}