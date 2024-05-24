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

namespace tp5er\think\auth\jwt\providers\storage;

use think\App;
use think\Cache;
use tp5er\think\auth\jwt\contracts\Storage;

class Think implements Storage
{
    /**
     * The cache repository contract.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The used cache tag.
     *
     * @var string
     */
    protected $tag = 'tp5er.auth.jwt';

    public function __construct(App $app)
    {
        $this->cache = $app->cache;
    }

    /**
     * Add a new item into storage.
     *
     * @param string $key
     * @param mixed $value
     * @param int $minutes
     *
     * @return void
     */
    public function add($key, $value, $minutes)
    {
        $this->cache->tag($this->tag)->set($key, $value, $minutes);
    }

    public function forever($key, $value)
    {
        $this->cache->tag($this->tag)->set($key, $value);
    }

    public function get($key)
    {
        return $this->cache->get($key);
    }

    public function destroy($key)
    {
        return $this->cache->delete($key);
    }

    public function flush()
    {
        $this->cache->tag($this->tag)->clear();
    }
}
