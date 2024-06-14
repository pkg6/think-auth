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

namespace tp5er\think\auth\permission;

use tp5er\think\auth\support\Collection;

class Guard
{
    /**
     * @param $model
     *
     * @return Collection|string
     *
     * @throws \ReflectionException
     */
    public static function getNames($model)
    {
        $class = is_object($model) ? get_class($model) : $model;
        if (is_object($model)) {
            if (\method_exists($model, 'guardName')) {
                $guardName = $model->guardName();
            } else {
                $guardName = $model->guard_name;
            }
        }
        if ( ! isset($guardName)) {
            $guardName = (new \ReflectionClass($class))->getDefaultProperties()['guard_name'] ?? null;
        }
        if ($guardName) {
            return Collection::make($guardName);
        }

        return self::getConfigAuthGuards($class);
    }

    protected static function getConfigAuthGuards($class): Collection
    {
        return Collection::make(config('auth.guards'))
            ->map(function ($guard) {
                if ( ! isset($guard['provider'])) {
                    return null;
                }

                return config("auth.providers.{$guard['provider']}.model");
            })->filter(function ($mode) use (&$class) {
                return $class === $mode;
            })->keys();
    }
}
