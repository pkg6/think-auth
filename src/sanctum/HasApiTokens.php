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

use think\helper\Str;
use think\model\relation\MorphMany;
use tp5er\think\auth\sanctum\contracts\HasAbilities;

trait HasApiTokens
{
    /**
     * The access token the user is using for the current request.
     *
     * @var HasAbilities
     */
    protected $accessToken;

    /**
     * Get the access tokens that belong to model.
     *
     * @return  MorphMany
     */
    public function tokens()
    {
        return $this->morphMany(Sanctum::$personalAccessTokenModel, 'tokenable');
    }

    public function createToken($name, array $abilities = ['*'])
    {
        $token = $this->tokens()->save([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($token, $token->getKey() . '|' . $plainTextToken);
    }
    /**
     * Determine if the current API token has a given scope.
     *
     * @param  string  $ability
     *
     * @return bool
     */
    public function tokenCan(string $ability)
    {
        return $this->accessToken && $this->accessToken->can($ability);
    }

    /**
     * Get the access token currently associated with the user.
     *
     * @return HasAbilities
     */
    public function currentAccessToken()
    {
        return $this->accessToken;
    }
    /**
     * Set the current access token for the user.
     *
     * @param  HasAbilities  $accessToken
     *
     * @return $this
     */
    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
