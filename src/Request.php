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

use Closure;

class Request extends \think\Request implements \tp5er\think\auth\contracts\Request
{
    /**
     * @var Closure
     */
    protected $userResolver;

    /**
     * Set the user resolver callback.
     *
     * @param \Closure $callback
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
     * @param string|null $guard
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
        $header = $this->header('Authorization', '');

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
        return $this->header("PHP_AUTH_PW");
    }

    /**
     * @return array|string|null
     */
    public function getUser()
    {
        return $this->header("PHP_AUTH_USER");
    }

    /**
     * @return array|mixed
     */
    public function userAgent()
    {
        return $this->get('User-Agent');
    }

    /**
     * Gets the user info.
     *
     * @return string|null A user name if any and, optionally, scheme-specific information about how to gain authorization to access the server
     */
    public function getUserInfo()
    {
        $userinfo = $this->getUser();
        $pass = $this->getPassword();
        if ('' != $pass) {
            $userinfo .= ":$pass";
        }

        return $userinfo;
    }

    /**
     * Determine if a header is set on the request.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasHeader($key)
    {
        return ! is_null($this->header($key));
    }

    /**
     * Checks whether or not the method is safe.
     *
     * @see https://tools.ietf.org/html/rfc7231#section-4.2.1
     *
     * @return bool
     */
    public function isMethodSafe()
    {
        return in_array($this->method(), ['GET', 'HEAD', 'OPTIONS', 'TRACE']);
    }

    /**
     * Checks whether or not the method is idempotent.
     *
     * @return bool
     */
    public function isMethodIdempotent()
    {
        return in_array($this->method(), ['HEAD', 'GET', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'PURGE']);
    }

    /**
     * Checks whether the method is cacheable or not.
     *
     * @see https://tools.ietf.org/html/rfc7231#section-4.2.3
     *
     * @return bool
     */
    public function isMethodCacheable()
    {
        return in_array($this->method(), ['GET', 'HEAD']);
    }
}
