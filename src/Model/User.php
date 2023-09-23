<?php

namespace tp5er\think\auth\Model;

use think\Model;
use tp5er\think\auth\Contracts\Authenticatable as AuthenticatableContract;


class User extends Model implements AuthenticatableContract
{
    use Authenticatable;
}