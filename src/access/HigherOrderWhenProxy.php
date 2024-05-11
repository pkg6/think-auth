<?php

/*
 * This file is part of the tp5er/think-auth
 *
 * (c) pkg6 <https://github.com/pkg6>
 *
 * (L) Licensed <https://opensource.org/license/MIT>
 *
 * (A) zhiqiang <https://www.zhiqiang.wang>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace tp5er\think\auth\access;

class HigherOrderWhenProxy
{
    /**
     * The collection being operated on.
     *
     * @var Collection
     */
    protected $collection;

    /**
     * The condition for proxying.
     *
     * @var bool
     */
    protected $condition;
    /**
     * @param Collection $collection
     * @param $condition
     */
    public function __construct(Collection $collection, $condition)
    {
        $this->condition = $condition;
        $this->collection = $collection;
    }
    /**
     * Proxy accessing an attribute onto the collection.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->condition
            ? $this->collection->{$key}
            : $this->collection;
    }

    /**
     * Proxy a method call onto the collection.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->condition
            ? $this->collection->{$method}(...$parameters)
            : $this->collection;
    }
}
