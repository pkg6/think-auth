<?php


namespace tp5er\think\auth\Guards;

use think\App;
use think\Event;
use think\Request;
use tp5er\think\auth\Contracts\StatefulGuard;
use tp5er\think\auth\Events\GuardEvent;
use tp5er\think\auth\Password\PasswordInterface;
use tp5er\think\auth\Traits\Macroable;
use tp5er\think\auth\UserProvider\UserProvider;

/**
 * Class Guard
 * @package tp5er\think\auth\Guards
 */
abstract class Guard implements StatefulGuard
{
    use GuardHelpers, GuardEvent, userProviderTrait, Macroable;
    /**
     * The name of the Guard. Typically "session".
     * Corresponds to guard name in authentication configuration.
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $config = [];
    /**
     * @var App
     */
    protected $app;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var UserProvider
     */
    protected $provider;

    /**
     * @var PasswordInterface
     */
    protected $password;

    /**
     * Guard constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config   = $config;
        $this->app      = App::getInstance();
        $this->name     = $this->app->get('auth.guard')->getName();
        $this->request  = $this->app->request;
        $this->provider = $this->getUserProvider();
        $this->password = $this->app->get('auth.password')->password();
        $this->provider->setPasswordVerify($this->password);
        $this->events = new Event($this->app);
        $this->initialize();
    }

    /**
     * 初始化
     */
    protected function initialize()
    {
    }
}