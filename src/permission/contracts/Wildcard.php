<?php

namespace tp5er\think\auth\permission\contracts;

interface Wildcard
{
    /**
     * @param  string|Wildcard  $permission
     */
    public function implies($permission);
}