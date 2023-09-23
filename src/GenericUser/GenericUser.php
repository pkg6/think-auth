<?php

namespace tp5er\think\auth\GenericUser;

use tp5er\think\auth\Model\Field;

/**
 * Class GenericUser
* @package tp5er\think\auth\GenericUser
 */
class GenericUser extends GenericUserAbstract
{

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->attributes[$this->getAuthIdentifierName()];
    }


    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->attributes[Field::$password];
    }


    /**
     * Get the "remember me" token value.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->attributes[$this->getRememberTokenName()];
    }


    /**
     * Set the "remember me" token value.
     *
     * @param string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->attributes[$this->getRememberTokenName()] = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}