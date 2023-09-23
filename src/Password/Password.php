<?php

namespace tp5er\think\auth\Password;

use tp5er\think\auth\Contracts\Authenticatable;



class Password implements PasswordInterface
{
    /**
     * @param Authenticatable $user
     * @param string $password
     * @return bool
     */
    public function verify(Authenticatable $user, string $password)
    {
        return password_verify($password, $user->getAuthPassword());
    }

    /**
     * @param string $password
     * @return string
     */
    public function encrypt(string $password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}