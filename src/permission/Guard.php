<?php

namespace tp5er\think\auth\permission;

use tp5er\think\auth\support\Collection;

class Guard
{
    public static function getNames($model): Collection
    {
        $class = is_object($model) ? get_class($model) : $model;
        if (is_object($model)) {
            if (\method_exists($model, 'guardName')) {
                $guardName = $model->guardName();
            } else {
                $guardName = $model->getAttr('guard_name');
            }
        }
        if (!isset($guardName)) {
            $guardName = (new \ReflectionClass($class))->getDefaultProperties()['guard_name'] ?? null;
        }
        if ($guardName) {
            return Collection::make($guardName);
        }
        return self::getConfigAuthGuards($class);
    }

    protected static function getConfigAuthGuards($class)
    {
        return Collection::make(config('auth.guards'))->map(function ($guard) {
            if (!isset($guard['provider'])) {
                return null;
            }
            return config("auth.providers.{$guard['provider']}.model");
        })->filter(function ($model) use ($class) {
            return $class === $model;
        })->keys();
    }

    public static function getDefaultName($class)
    {
        $default = config('auth.defaults.guard');
        $possible_guards = static::getNames($class);
        if ($possible_guards->contains($default)) {
            return $default;
        }
        return $possible_guards->first() ?: $default;
    }
}