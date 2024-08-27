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

namespace tp5er\think\auth;

use BadMethodCallException;
use think\App;
use tp5er\think\auth\contracts\Guard;
use tp5er\think\auth\contracts\UserProvider;
use tp5er\think\auth\jwt\exceptions\JWTException;
use tp5er\think\auth\jwt\exceptions\UserNotDefinedException;
use tp5er\think\auth\jwt\JWTAuth;
use tp5er\think\auth\support\Macroable;

class JWTGuard implements Guard
{
    use GuardHelpers, Macroable {
        __call as macroCall;
    }

    /**
     * @var App
     */
    protected $app;

    /**
     * @var JWTAuth
     */
    protected $jwt;
    /**
     * @var \think\Request
     */
    protected $request;

    /**
     * @var contracts\Authenticatable|null
     */
    protected $lastAttempted;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @param App $app
     * @param $name
     * @param UserProvider $provider
     * @param JWTAuth $jwt
     */
    public function __construct(
        App          $app,
        $name,
        UserProvider $provider,
        JWTAuth      $jwt
    ) {
        $this->app = $app;
        $this->name = $name;
        $this->provider = $provider;
        $this->request = $this->app->request;
        $this->jwt = $jwt;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return contracts\Authenticatable|void|null
     */
    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }
        if ($this->jwt->setRequest($this->request)->getToken() &&
            ($payload = $this->jwt->check(true)) &&
            $this->validateSubject()
        ) {
            return $this->user = $this->provider->retrieveById($payload['sub']);
        }
    }

    /**
     * Get the currently authenticated user or throws an exception.
     *
     * @return \tp5er\think\auth\contracts\Authenticatable
     *
     * @throws UserNotDefinedException
     */
    public function userOrFail()
    {
        if ( ! $user = $this->user()) {
            throw new UserNotDefinedException;
        }

        return $user;
    }

    /**
     * @param array $credentials
     * @param $login
     *
     * @return bool|string
     */
    public function attempt(array $credentials = [], $login = true)
    {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);
        if ($this->hasValidCredentials($user, $credentials)) {
            return $login ? $this->login($user) : true;
        }

        return false;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return (bool) $this->attempt($credentials, false);
    }

    /**
     * @param $user
     *
     * @return string
     */
    public function login($user)
    {
        $token = $this->jwt->fromUser($user);
        $this->setToken($token)->setUser($user);

        return $token;
    }

    /**
     * Logout the user, thus invalidating the token.
     *
     * @param bool $forceForever
     *
     * @return void
     *
     * @throws JWTException
     */
    public function logout($forceForever = false)
    {
        $this->requireToken()->invalidate($forceForever);
        $this->user = null;
        $this->jwt->unsetToken();
    }

    /**
     * Ensure that a token is available in the request.
     *
     * @return JWTAuth
     *
     * @throws JWTException
     */
    protected function requireToken()
    {
        if ( ! $this->jwt->setRequest($this->request)->getToken()) {
            throw new JWTException('Token could not be parsed from the request.');
        }

        return $this->jwt;
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param mixed $user
     * @param array $credentials
     *
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        return $user !== null && $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * @param $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->jwt->setToken($token);

        return $this;
    }

    /**
     * Set the token ttl.
     *
     * @param int $ttl
     *
     * @return $this
     */
    public function setTTL($ttl)
    {
        $this->jwt->factory()->setTTL($ttl);

        return $this;
    }

    /**
     * Ensure the JWTSubject matches what is in the token.
     *
     * @return  bool
     */
    protected function validateSubject()
    {
        // If the provider doesn't have the necessary method
        // to get the underlying model name then allow.
        if ( ! method_exists($this->provider, 'getModel')) {
            return true;
        }

        return $this->jwt->checkSubjectModel($this->provider->getModel());
    }

    /**
     * Magically call the JWT instance.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {

        if (method_exists($this->jwt, $method)) {
            return call_user_func_array([$this->jwt, $method], $parameters);
        }

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }
        throw new BadMethodCallException("Method [$method] does not exist.");
    }
}
