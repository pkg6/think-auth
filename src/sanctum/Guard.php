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

use think\App;
use think\helper\Arr;
use think\Model;
use think\Request;
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\sanctum\events\TokenAuthenticated;
use tp5er\think\auth\support\Req;
use tp5er\think\auth\support\Timer;

class Guard
{
    /**
     * @var App
     */
    protected $app;
    /**
     * The authentication factory implementation.
     *
     * @var Factory
     */
    protected $auth;

    /**
     * The number of minutes tokens should be allowed to remain valid.
     *
     * @var int
     */
    protected $expiration;

    /**
     * The provider name.
     *
     * @var string
     */
    protected $provider;

    /**
     * @param Factory $auth
     * @param null $expiration
     * @param mixed $provider
     */
    public function __construct(App $app, Factory $auth, $expiration = null, $provider = null)
    {
        $this->app = $app;
        $this->auth = $auth;
        $this->expiration = $expiration;
        $this->provider = $provider;
    }

    public function __invoke(Request $request)
    {

        $guards = $this->app->config->get('auth.sanctum.guard', "web");
        foreach (Arr::wrap($guards) as $guard) {
            $user = $this->auth->guard($guard)->user();
            if ($user) {
                if ($this->supportsTokens($user)) {
                    if (method_exists($user, 'withAccessToken')) {
                        $user->withAccessToken(new TransientToken);
                    }
                } else {
                    return $user;
                }
            }
        }
        if ($token = $this->getTokenFromRequest($request)) {
            /**
             * @var PersonalAccessToken $model
             */
            $model = Sanctum::$personalAccessTokenModel;
            $accessToken = $model::findToken($token);
            if ( ! $this->isValidAccessToken($accessToken) ||
                ! $this->supportsTokens($accessToken->tokenable)) {
                return null;
            }
            if ($accessToken->tokenable) {
                //触发事件
                $this->app->event->trigger(new TokenAuthenticated($accessToken));
                if (method_exists($accessToken->tokenable, "withAccessToken")) {
                    $tokenable = $accessToken->tokenable->withAccessToken($accessToken);
                }
                $model->saveLastUsedAt();

                return $tokenable;
            }
        }

        return null;
    }

    /**
     * Determine if the tokenable model supports API tokens.
     *
     * @param mixed $tokenable
     *
     * @return bool
     */
    protected function supportsTokens($tokenable = null)
    {
        return $tokenable && in_array(HasApiTokens::class, class_uses_recursive(get_class($tokenable)));
    }

    protected function getTokenFromRequest(Request $request)
    {
        if (is_callable(Sanctum::$accessTokenRetrievalCallback)) {
            return (string) (Sanctum::$accessTokenRetrievalCallback)($request);
        }

        return Req::bearerToken($request);
    }

    /**
     * Determine if the provided access token is valid.
     *
     * @param PersonalAccessToken $accessToken
     *
     * @return bool
     */
    protected function isValidAccessToken($accessToken)
    {
        if ( ! $accessToken) {
            return false;
        }

        $isValid =
            ( ! $this->expiration || $accessToken->getCreateTimeTimestamp() > Timer::timeAddSec($this->expiration))
            && $this->hasValidProvider($accessToken->getTokenable());
        if (is_callable(Sanctum::$accessTokenAuthenticationCallback)) {
            $isValid = (bool) (Sanctum::$accessTokenAuthenticationCallback)($accessToken, $isValid);
        }

        return $isValid;
    }

    protected function hasValidProvider($tokenable)
    {
        if (is_null($this->provider)) {
            return true;
        }
        $model = $this->app->config->get("auth.providers.{$this->provider}.model");

        return $tokenable instanceof $model;
    }
}
