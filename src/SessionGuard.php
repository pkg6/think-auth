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

use InvalidArgumentException;
use think\App;
use think\Cookie;
use think\helper\Str;
use think\Request;
use think\Session;
use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\contracts\StatefulGuard;
use tp5er\think\auth\contracts\UserProvider;
use tp5er\think\auth\events\CurrentDeviceLogout;
use tp5er\think\auth\events\Logout;
use tp5er\think\auth\exceptions\UnauthorizedHttpException;
use tp5er\think\auth\support\Recaller;
use tp5er\think\auth\support\Timebox;
use tp5er\think\hashing\facade\Hash;

class SessionGuard implements StatefulGuard
{
    use GuardHelpers, GuardEventHelper;

    /**
     * @var App
     */
    protected $app;
    /**
     * The name of the guard. Typically "web".
     *
     * Corresponds to guard name in authentication configuration.
     *
     * @var string
     */
    protected $name;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var Request|null
     */
    protected $request;
    /**
     * @var UserProvider
     */
    protected $provider;
    /**
     * @var Timebox
     */
    protected $timebox;
    /**
     * @var Cookie
     */
    protected $cookie;

    /**
     * The number of minutes that the "remember me" cookie should be valid for.
     *
     * @var int
     */
    protected $rememberDuration = 2628000;
    /**
     * The user we last attempted to retrieve.
     *
     * @var Authenticatable
     */
    protected $lastAttempted;

    /**
     * Indicates if the user was authenticated via a recaller cookie.
     *
     * @var bool
     */
    protected $viaRemember = false;

    /**
     * Indicates if the logout method has been called.
     *
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
     * @param App $app
     * @param $name
     * @param UserProvider $provider
     * @param Request|null $request
     * @param Timebox|null $timebox
     */
    public function __construct(
        App          $app,
        $name,
        UserProvider $provider,
        Request      $request = null,
        Timebox      $timebox = null
    ) {
        $this->app = $app;
        $this->name = $name;
        $this->session = $app->session;
        $this->request = $request;
        $this->provider = $provider;
        $this->timebox = $timebox ?: new Timebox;
    }

    /**
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the cookie creator instance used by the guard.
     *
     * @param Cookie $cookie
     *
     * @return void
     */
    public function setCookie(Cookie $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * @return Cookie
     */
    protected function getCookie()
    {
        if (isset($this->cookie)) {
            return $this->cookie;
        }

        return $this->app->cookie;
    }

    /**
     * Set the number of minutes the remember me cookie should be valid for.
     *
     * @param int $minutes
     *
     * @return $this
     */
    public function setRememberDuration($minutes)
    {
        $this->rememberDuration = $minutes;

        return $this;
    }

    /**
     * Get the number of minutes the remember me cookie should be valid for.
     *
     * @return int
     */
    protected function getRememberDuration()
    {
        return $this->rememberDuration;
    }

    /**
     * @return mixed|Authenticatable|void|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return;
        }
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if ( ! is_null($this->user)) {
            return $this->user;
        }

        $id = $this->session->get($this->getSessionName());

        // First we will try to load the user using the identifier in the session if
        // one exists. Otherwise we will check for a "remember me" cookie in this
        // request, and if one exists, attempt to retrieve the user using that.
        if ( ! is_null($id) && $this->user = $this->provider->retrieveById($id)) {
            $this->fireAuthenticatedEvent($this->user);
        }
        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        if (is_null($this->user) && ! is_null($recaller = $this->recaller())) {
            $this->user = $this->userFromRecaller($recaller);
            if ($this->user) {
                $this->updateSession($this->user->getAuthIdentifier());
                $this->fireLoginEvent($this->user, true);
            }
        }

        return $this->user;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
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
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        return $this->hasValidCredentials($user, $credentials);
    }

    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @param string $field
     * @param array $extraConditions
     *
     * @return void
     */
    public function basic($field = 'email', $extraConditions = [])
    {
        if ($this->check()) {
            return;
        }
        // If a username is set on the HTTP basic request, we will return out without
        // interrupting the request lifecycle. Otherwise, we'll need to generate a
        // request indicating that the given credentials were invalid for login.
        if ($this->attemptBasic($this->app->request, $field, $extraConditions)) {
            return;
        }

        $this->failedBasicResponse();
    }

    /**
     * Perform a stateless HTTP Basic login attempt.
     *
     * @param string $field
     * @param array $extraConditions
     *
     * @return void
     */
    public function onceBasic($field = 'email', $extraConditions = [])
    {
        $credentials = $this->basicCredentials($this->app->request, $field);

        if ( ! $this->once(array_merge($credentials, $extraConditions))) {
            $this->failedBasicResponse();
        }
    }

    /**
     * Get the response for basic authentication.
     *
     * @return void
     *
     * @throws UnauthorizedHttpException
     */
    protected function failedBasicResponse()
    {
        throw new UnauthorizedHttpException('Basic', 'Invalid credentials.');
    }

    /**
     * Attempt to authenticate using basic authentication.
     *
     * @param Request $request
     * @param string $field
     * @param array $extraConditions
     *
     * @return bool
     */
    public function attemptBasic(Request $request, $field, $extraConditions = [])
    {
        if ( ! requestGetUser()) {
            return false;
        }

        return $this->attempt(array_merge(
            $this->basicCredentials($request, $field),
            $extraConditions
        ));
    }

    /**
     * Get the credential array for an HTTP Basic request.
     *
     * @param Request $request
     * @param string $field
     *
     * @return array
     */
    protected function basicCredentials(Request $request, $field)
    {
        return [
            $field => requestGetUser(),
            'password' => requestGetPassword()
        ];
    }

    /**
     * @param array $credentials
     * @param $remember
     *
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
     * @param array $credentials
     *
     * @return bool
     */
    public function once(array $credentials = [])
    {
        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);

            return true;
        }

        return false;
    }

    /**
     * @param Authenticatable $user
     * @param $remember
     *
     * @return void
     */
    public function login(Authenticatable $user, $remember = false)
    {
        $this->updateSession($user->getAuthIdentifier());
        // If the user should be permanently "remembered" by the application we will
        // queue a permanent cookie that contains the encrypted copy of the user
        // identifier. We will then decrypt this later to retrieve the users.
        if ($remember) {
            if (empty($user->getRememberToken())) {
                $this->cycleRememberToken($user);
            }
            $this->queueRecallerCookie($user);
        }
        $this->fireLoginEvent($user, $remember);
        $this->setUser($user);
    }

    /**
     * Log the given user ID into the application.
     *
     * @param mixed $id
     * @param bool $remember
     *
     * @return Authenticatable|false
     */
    public function loginUsingId($id, $remember = false)
    {
        if ( ! is_null($user = $this->provider->retrieveById($id))) {
            $this->login($user, $remember);

            return $user;
        }

        return false;
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param mixed $id
     *
     * @return Authenticatable|false
     */
    public function onceUsingId($id)
    {
        if ( ! is_null($user = $this->provider->retrieveById($id))) {
            $this->setUser($user);

            return $user;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function viaRemember()
    {
        return $this->viaRemember;
    }

    /**
     * @return void
     */
    public function logout()
    {
        $user = $this->user();
        $this->clearUserDataFromStorage();

        if ( ! is_null($this->user) && ! empty($user->getRememberToken())) {
            $this->cycleRememberToken($user);
        }
        $this->app->event->trigger(new Logout($this->name, $user));
        $this->user = null;
        $this->loggedOut = true;
    }

    /**
     * Update the session with the given ID.
     *
     * @param string $id
     *
     * @return void
     */
    protected function updateSession($id)
    {
        $this->session->set($this->getSessionName(), $id);
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getSessionName()
    {
        return 'login_' . $this->name . '_' . sha1(static::class);
    }

    /**
     * Refresh the "remember me" token for the user.
     *
     * @param Authenticatable $user
     *
     * @return void
     */
    protected function cycleRememberToken(Authenticatable $user)
    {
        $user->setRememberToken($token = Str::random(60));
        $this->provider->updateRememberToken($user, $token);
    }

    /**
     * Queue the recaller cookie into the cookie jar.
     *
     * @param Authenticatable $user
     *
     * @return void
     */
    protected function queueRecallerCookie(Authenticatable $user)
    {
        $this->getCookie()->set(
            $this->getRecallerName(),
            $user->getAuthIdentifier() . '|' . $user->getRememberToken() . '|' . $user->getAuthPassword(),
            ["expire" => $this->getRememberDuration()]
        );
    }

    /**
     * @return string
     */
    protected function getRecallerName()
    {
        return 'remember_' . $this->name . '_' . sha1(static::class);
    }

    /**
     * @param Authenticatable $user
     * @param array $credentials
     *
     * @return mixed
     */
    protected function hasValidCredentials(Authenticatable $user, array $credentials)
    {
        return $this->timebox->call(function ($timebox) use ($user, $credentials) {
            $validated = ! is_null($user) && $this->provider->validateCredentials($user, $credentials);

            if ($validated) {
                $timebox->returnEarly();
            }

            return $validated;
        }, 200 * 1000);
    }

    /**
     * Pull a user from the repository by its "remember me" cookie token.
     *
     * @param Recaller $recaller
     *
     * @return mixed
     */
    protected function userFromRecaller($recaller)
    {
        if ( ! $recaller->valid() || $this->recallAttempted) {
            return null;
        }
        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        $this->recallAttempted = true;

        $this->viaRemember = ! is_null($user = $this->provider->retrieveByToken(
            $recaller->id(),
            $recaller->token()
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
            return null;
        }
        if ($recaller = $this->cookie->get($this->getRecallerName())) {
            return new Recaller($recaller);
        }

        return null;
    }

    /**
     * @return void
     */
    protected function clearUserDataFromStorage()
    {
        $this->session->delete($this->getSessionName());
        if ( ! is_null($this->recaller())) {
            $this->getCookie()->delete($this->getRecallerName());
        }
    }

    /**
     * @param Authenticatable $user
     *
     * @return $this|void
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        $this->loggedOut = false;
        $this->fireAuthenticatedEvent($user);

        return $this;
    }

    /**
     * Set the current request instance.
     *
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
     * Log the user out of the application on their current device only.
     *
     * This method does not cycle the "remember" token.
     *
     * @return void
     */
    public function logoutCurrentDevice()
    {
        $user = $this->user();
        $this->clearUserDataFromStorage();
        $this->app->event->trigger(new CurrentDeviceLogout($this->name, $user));
        $this->user = null;

        $this->loggedOut = true;
    }

    /**
     * Invalidate other sessions for the current user.
     *
     * The application must be using the AuthenticateSession middleware.
     *
     * @param string $password
     * @param string $attribute
     *
     * @return Authenticatable|null
     */
    public function logoutOtherDevices($password, $attribute = 'password')
    {
        if ( ! $this->user()) {
            return null;
        }
        $result = $this->rehashUserPassword($password, $attribute);

        if ($this->recaller() ||
            $this->getCookie()->has($this->getRecallerName())) {
            $this->queueRecallerCookie($this->user());
        }

        $this->fireOtherDeviceLogoutEvent($this->user());

        return $result;
    }

    /**
     * Rehash the current user's password.
     *
     * @param string $password
     * @param string $attribute
     *
     * @return Authenticatable|null
     *
     * @throws \InvalidArgumentException
     */
    public function rehashUserPassword($password, $attribute)
    {
        if ( ! Hash::check($password, $this->user()->{$attribute})) {
            throw new InvalidArgumentException('The given password does not match the current password.');
        }

        if ($this->user() instanceof \think\Model) {
            $this->user()->save([$attribute => Hash::make($password),]);
        }

        return $this->user();
    }

}
