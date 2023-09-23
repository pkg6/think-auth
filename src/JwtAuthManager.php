<?php

namespace tp5er\think\auth;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use think\App;
use tp5er\think\auth\Exceptions\LoginException;
use tp5er\think\auth\Guards\Guard;
use tp5er\think\auth\Support\Payload;
use tp5er\think\auth\Traits\RequestToken;


class JwtAuthManager
{
    use RequestToken;
    /**
     * @var App
     */
    protected $app;
    /**
     * @var Guard
     */
    protected $auth;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var CacheInterface
     */
    protected $storage;

    /**
     * @var JwtFactory
     */
    protected $factory;
    /**
     * @var Payload
     */
    private $payload;

    /**
     * JwtAuthManager constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app      = $app;
        $this->request  = $this->app->request;
        $this->storage  = $this->app->cache;
        $this->factory  = $this->app->get('jwt.factory');
    }

    /**
     * @param null $name
     * @return $this
     */
    public function setAuth($name = null)
    {
        $this->auth = $this->app->get('auth.guard')->guard($name);
        return $this;
    }

    /**
     * @return Guard
     */
    public function auth()
    {
        if (is_null($this->auth)) {
            $this->setAuth();
        }
        return $this->auth;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken(string $token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestToken()
    {
        if (is_null($this->token)) {
            $this->parseToken();
        }
        return $this->token;
    }

    /**
     * @return Payload
     */
    public function getPayload()
    {
        $this->getRequestToken();
        if (empty($this->payload)) {
            $this->payload = $this->factory->getPayload($this->token);
        }
        return $this->payload;
    }

    /**
     * @return int
     * @throws InvalidArgumentException
     */
    public function id()
    {
        $this->getRequestToken();
        return $this->storage->get($this->cacheKey());
    }

    /**
     * @param array $credentials
     * @return string
     * @throws LoginException
     * @throws InvalidArgumentException
     */
    public function attempt(array $credentials = [])
    {
        if (!$this->auth()->attempt($credentials)) {
            throw new LoginException('failed to login');
        }
        $sub   = $this->auth()->id();
        $token = $this->factory->buildToken([
            'sub' => $sub
        ]);
        $this->storage->set($this->cacheKey($token), $sub);
        return $token;
    }

    /**
     * @return bool|Contracts\Authenticatable|null
     * @throws InvalidArgumentException
     */
    public function authenticate()
    {
        $id = $this->id();
        if (!$this->auth()->onceUsingId($id)) {
            return false;
        }
        return $this->user();
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function refresh()
    {
        $this->getRequestToken();
        $token = $this->factory->refresh($this->token);
        $sub   = $this->factory->getSub();
        $this->storage->set($this->cacheKey(), $sub);
        return $token;
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function logout()
    {
        $this->getRequestToken();
        $this->storage->delete($this->cacheKey());
        $this->auth()->logout();
    }

    /**
     * @return Contracts\Authenticatable|null
     */
    protected function user()
    {
        return $this->auth()->user();
    }


    /**
     * @param null $token
     * @return string
     */
    protected function cacheKey($token = null)
    {
        return 'jwt_login_' . crc32($token ?? $this->token);
    }
}