<?php

namespace tp5er\think\auth\UserProvider;

use tp5er\think\auth\Contracts\Authenticatable;
use tp5er\think\auth\GenericUser\GenericUser;
use tp5er\think\auth\Model\Field;
use tp5er\think\auth\Password\PasswordInterface;


abstract class UserProvider
{
    /**
     * @var PasswordInterface
     */
    protected $passwordVerify;

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     * @return Authenticatable|null
     */
    abstract public function retrieveById($identifier);

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed $identifier
     * @param string $token
     * @return Authenticatable|null
     */
    abstract public function retrieveByToken($identifier, $token);

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param Authenticatable $user
     * @param string $token
     * @return void
     */
    abstract public function updateRememberToken(Authenticatable $user, $token);

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return Authenticatable|null
     */
    abstract public function retrieveByCredentials(array $credentials);


    /**
     * Validate a user against the given credentials.
     *
     * @param Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->passwordVerify->verify($user, $credentials[Field::$password]);
    }


    /**
     * @param PasswordInterface $passwordVerify
     * @return $this
     */
    public function setPasswordVerify(PasswordInterface $passwordVerify)
    {
        $this->passwordVerify = $passwordVerify;
        return $this;
    }

    /**
     * Get the generic user.
     *
     * @param mixed $user
     * @return Authenticatable|null
     */
    protected function getGenericUser($user)
    {
        if (!is_null($user)) {
            return new GenericUser((array)$user);
        }
    }
}