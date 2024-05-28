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
use Closure;
use think\App;
use think\Request;

class AuthRequest
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Closure
     */
    protected $userResolver;
    /**
     * @var App
     */
    protected $app;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set the user resolver callback.
     *
     * @param  \Closure  $callback
     *
     * @return $this
     */
    public function setUserResolver(Closure $callback)
    {
        $this->userResolver = $callback;

        return $this;
    }

    /**
     * Get the user making the request.
     *
     * @param  string|null  $guard
     *
     * @return mixed
     */
    public function user($guard = null)
    {
        return call_user_func($this->getUserResolver(), $guard);
    }

    /**
     * Get the user resolver callback.
     *
     * @return \Closure
     */
    public function getUserResolver()
    {
        return $this->userResolver ?: function () {
            //
        };
    }

    /**
     * Get the bearer token from the request headers.
     *
     * @return string|null
     */
    public function bearerToken()
    {
        $header = $this->request->header('Authorization', '');

        $position = strrpos($header, 'Bearer ');

        if ($position !== false) {
            $header = substr($header, $position + 7);

            return strpos($header, ',') !== false ? strstr($header, ',', true) : $header;
        }
    }

    /**
     * @return array|string|null
     */
    public function getPassword()
    {
        return $this->request->header("PHP_AUTH_PW");
    }

    /**
     * @return array|string|null
     */
    public function getUser()
    {
        return $this->request->header("PHP_AUTH_USER");
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
        if (method_exists($this->request, $method)) {
            return call_user_func_array([$this->request, $method], $parameters);
        }
        throw new BadMethodCallException("Method [$method] does not exist.");
    }
}
