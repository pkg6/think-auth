<?php

namespace tp5er\think\auth\Guards;

use think\Cookie;
use think\helper\Str;
use think\Session;
use tp5er\think\auth\Contracts\Authenticatable;
use tp5er\think\auth\Support\Recaller;

/**
 * Class SessionGuard
 * @package tp5er\think\auth\Guards
 */
class SessionGuard extends Guard
{

    /**
     * @var Session
     */
    protected $session;
    /**
     * @var Cookie
     */
    protected $cookie;
    /**
     * @var Authenticatable|null
     */
    protected $lastAttempted;

    /**
     * @var bool
     */
    protected $loggedOut = false;
    /**
     * Indicates if a token user retrieval has been attempted.
     *
     * @var bool
     */
    protected $recallAttempted = false;

    /**
     * Indicates if the user was authenticated via a recaller cookie.
     *
     * @var bool
     */
    protected $viaRemember = false;


    protected function initialize()
    {
        $this->session = $this->app->session;
        $this->cookie  = $this->app->cookie;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        if ($this->loggedOut) {
            return;
        }
        return $this->user()
            ? $this->user()->getAuthIdentifier()
            : $this->session->get($this->getName());
    }

    /**
     * Set the current user.
     *
     * @param Authenticatable $user
     * @return $this
     */
    public function setUser(Authenticatable $user)
    {
        $this->user      = $user;
        $this->loggedOut = false;
        $this->fireAuthenticatedEvent($user);
        return $this;
    }


    /**
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $this->fireAttemptEvent($credentials, $remember);
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);
        if ($this->hasValidCredentials($user, $credentials)) {
            $this->login($user, $remember);
            return true;
        }
        $this->fireFailedEvent($user, $credentials);
        return false;
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param array $credentials
     * @return bool
     */
    public function once(array $credentials = [])
    {
        $this->fireAttemptEvent($credentials);
        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);
            return true;
        }
        return false;
    }

    /**
     * Log the given user ID into the application.
     *
     * @param mixed $id
     * @param bool $remember
     * @return Authenticatable|false
     */
    public function loginUsingId($id, $remember = false)
    {
        if (!is_null($user = $this->provider->retrieveById($id))) {
            $this->login($user, $remember);

            return $user;
        }
        return false;
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param mixed $id
     * @return Authenticatable|false
     */
    public function onceUsingId($id)
    {
        if (!is_null($user = $this->provider->retrieveById($id))) {
            $this->setUser($user);
            return $user;
        }
        return false;
    }

    /**
     * Determine if the user was authenticated via "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember()
    {
        return $this->viaRemember;
    }

    /**
     * Log a user into the application.
     *
     * @param Authenticatable $user
     * @param bool $remember
     * @return void
     */
    public function login(Authenticatable $user, $remember = false)
    {
        $this->updateSession($user->getAuthIdentifier());
        // 如果用户应该被应用程序永久“记住”，我们将
        // 将包含用户加密副本的永久cookie
        // 标识符。 我们稍后将对其进行解密以检索用户。
        if ($remember) {
            $this->ensureRememberTokenIsSet($user);

            $this->queueRecallerCookie($user);
        }
        // 如果我们设置了一个事件调度器实例，我们将触发一个事件，以便
        // 任何侦听器都将连接到身份验证事件并运行操作
        // 基于从守卫实例触发的登录和注销事件。
        $this->fireLoginEvent($user, $remember);
        $this->setUser($user);
    }


    /**
     * Get the currently authenticated user.
     * @return Authenticatable|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return;
        }
        // 如果我们已经为当前请求检索了用户，我们可以
        // 立即返回。 我们不想获取用户数据
        // 每次调用此方法，因为那会非常慢。
        if (!is_null($this->user)) {
            return $this->user;
        }
        $id = $this->session->get($this->getName());
        if (!is_null($id) && $this->user = $this->provider->retrieveById($id)) {
            $this->fireAuthenticatedEvent($this->user);
        }
        // 如果用户为空，但我们解密了一个“召回者”cookie，我们可以尝试
        // 拉取该 cookie 上的用户数据，作为记住 cookie
        // 应用程序。 一旦我们有了用户，我们就可以将其返回给调用者。
        if (is_null($this->user) && !is_null($recaller = $this->recaller())) {
            $this->user = $this->userFromRecaller($recaller);
            if ($this->user) {
                $this->updateSession($this->user->getAuthIdentifier());
                $this->fireLoginEvent($this->user, true);
            }
        }
        return $this->user;
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $user = $this->user();
        $this->clearUserDataFromStorage();

        if (!is_null($this->user) && !empty($user->getRememberToken())) {
            $this->cycleRememberToken($user);
        }
        // 一旦我们触发了注销事件，我们将清除用户的内存
        // 所以它们不再可用，因为用户不再被视为
        // 正在登录此应用程序，此处不可用。

        $this->currentDeviceLogout($user);

        $this->user      = null;
        $this->loggedOut = true;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);
        return $this->hasValidCredentials($user, $credentials);

    }

    /**
     * Remove the user data from the session and cookies.
     *
     * @return void
     */
    protected function clearUserDataFromStorage()
    {
        $this->session->delete($this->getName());
    }

    /**
     * Pull a user from the repository by its "remember me" cookie token.
     *
     * @param Recaller $recaller
     * @return mixed
     */
    protected function userFromRecaller($recaller)
    {
        if (!$recaller->valid() || $this->recallAttempted) {
            return;
        }
        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        $this->recallAttempted = true;

        $this->viaRemember = !is_null($user = $this->provider->retrieveByToken(
            $recaller->id(), $recaller->token()
        ));

        return $user;
    }

    /**
     * Get the decrypted recaller cookie for the request.
     *
     * @return Recaller|null
     */
    protected function recaller()
    {
        if (is_null($this->request)) {
            return;
        }
        if ($recaller = $this->cookie->get($this->getRecallerName())) {
            return new Recaller($recaller);
        }
    }


    /**
     * Queue the recaller cookie into the cookie jar.
     *
     * @param Authenticatable $user
     * @return void
     */
    protected function queueRecallerCookie(Authenticatable $user)
    {
        $value = $user->getAuthIdentifier() . '|' . $user->getRememberToken() . '|' . $user->getAuthPassword();
        $this->cookie->forever($this->getRecallerName(), $value);
    }

    /**
     * Create a new "remember me" token for the user if one doesn't already exist.
     *
     * @param Authenticatable $user
     * @return void
     */
    protected function ensureRememberTokenIsSet(Authenticatable $user)
    {
        if (empty($user->getRememberToken())) {
            $this->cycleRememberToken($user);
        }
    }

    /**
     * Refresh the "remember me" token for the user.
     *
     * @param Authenticatable $user
     * @return void
     */
    protected function cycleRememberToken(Authenticatable $user)
    {
        $user->setRememberToken($token = Str::random(60));
        $this->provider->updateRememberToken($user, $token);
    }

    /**
     * Update the session with the given ID.
     *
     * @param string $id
     * @return void
     */
    protected function updateSession($id)
    {
        $this->session->set($this->getName(), $id);
        $this->session->regenerate(true);
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param mixed $user
     * @param array $credentials
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        $validated = !is_null($user) && $this->provider->validateCredentials($user, $credentials);
        if ($validated) {
            $this->fireValidatedEvent($user);
        }
        return $validated;
    }


    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return 'login_' . $this->name . '_' . sha1(static::class);
    }

    /**
     * Get the name of the cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName()
    {
        return 'remember_' . $this->name . '_' . sha1(static::class);
    }
}