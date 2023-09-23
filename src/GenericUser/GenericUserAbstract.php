<?php

namespace tp5er\think\auth\GenericUser;

use tp5er\think\auth\Contracts\Authenticatable;

/**
 * Class GenericUserAbstract
* @package tp5er\think\auth\GenericUser
 */
abstract class GenericUserAbstract implements Authenticatable
{
    /**
     * All of the user's attributes.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Create a new generic User object.
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->attributes);
    }

    /**
     * Dynamically access the user's attributes.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Dynamically set an attribute on the user.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Dynamically check if a value is set on the user.
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Dynamically unset a value on the user.
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }
}