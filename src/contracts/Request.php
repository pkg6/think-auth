<?php

namespace tp5er\think\auth\contracts;

use Closure;

interface Request
{
    public function setUserResolver(Closure $callback);

    public function user($guard = null);

    public function getUserResolver();
}
