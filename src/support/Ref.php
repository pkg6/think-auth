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

namespace tp5er\think\auth\support;

class Ref
{

    public static function getClassConstValue($objectOrClass, $name)
    {
        return (new \ReflectionClass($objectOrClass))->getConstant($name);
    }
}
