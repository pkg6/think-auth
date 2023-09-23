<?php

namespace tp5er\think\auth;

use LogicException;
use think\App;
use tp5er\think\auth\Contracts\Authenticatable;
use tp5er\think\auth\Password\PasswordInterface;


class PasswordManager
{
    /**
     * @var PasswordInterface
     */
    private $password;

    /**
     * PasswordVerifyManager constructor.
     * @param App $app
     * @throws LogicException
     */
    public function __construct(App $app)
    {
        $password = $app->config->get('auth.password');
        if (!class_exists($password)) {
            throw new LogicException('auth.password Not a class');
        }
        $p = new $password();
        if (!($p instanceof PasswordInterface)) {
            throw new LogicException('auth.password implements PasswordInterface');
        }
        $this->password = $p;
    }

    /**
     * @return PasswordInterface
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return string
     */
    public function encrypt(string $password)
    {
        return $this->password->encrypt($password);
    }

    /**
     * @param Authenticatable $user
     * @param string $password
     * @return bool
     */
    public function verify(Authenticatable $user, string $password)
    {
        return $this->password->verify($user, $password);
    }
}