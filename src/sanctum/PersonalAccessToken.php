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

namespace tp5er\think\auth\sanctum;

use think\Model;
use think\model\relation\MorphTo;
use tp5er\think\auth\sanctum\contracts\HasAbilities;

class PersonalAccessToken extends Model implements HasAbilities
{
    protected $json = ['abilities'];

    /**
     * @return false|string
     */
    public function getCreateTimeTimestamp()
    {
        return $this->createTime;
    }

    public function saveLastUsed()
    {
        $this->last_used_time = time();

        return $this->save();
    }

    /**
     * Get the tokenable model that the access token belongs to.
     *
     * @return MorphTo
     */
    public function tokenable()
    {
        return $this->morphTo('tokenable');
    }

    /**
     * @inheritDoc
     */
    public static function findToken($token)
    {
        if (strpos($token, '|') === false) {
            return static::where('token', hash('sha256', $token))->find();
        }

        [$id, $token] = explode('|', $token, 2);

        if ($instance = static::find($id)) {
            return hash_equals($instance->token, hash('sha256', $token)) ? $instance : null;
        }

        return null;
    }

    public function getTokenable()
    {
        return $this->tokenable;
    }

    /**
     * Determine if the token has a given ability.
     *
     * @param  string  $ability
     *
     * @return bool
     */
    public function can($ability)
    {
        return in_array('*', $this->abilities) ||
            array_key_exists($ability, array_flip($this->abilities));
    }

    /**
     * Determine if the token is missing a given ability.
     *
     * @param  string  $ability
     *
     * @return bool
     */
    public function cant($ability)
    {
        return ! $this->can($ability);
    }
}
