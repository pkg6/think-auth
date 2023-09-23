<?php

namespace tp5er\think\auth\Encrypt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Class JwtEncrypt
 * @package tp5er\think\auth\Encrypt
 */
class JwtEncrypt implements EncryptInterface
{
    /**
     * @var Key
     */
    protected $key;

    /**
     * @var int
     */
    protected $leeway;

    public function __construct()
    {
        $this->leeway              = $this->getLeeway();
        JWT::$leeway = $this->leeway;
    }

    /**
     * @return int
     */
    public function getLeeway(): int
    {
        return $this->leeway ?? 60;
    }

    /**
     * @param int $leeway
     * @return $this
     */
    public function setLeeway(int $leeway)
    {
        $this->leeway = $leeway;
        return $this;
    }

    /**
     * @param $keyMaterial
     * @param $algorithm
     * @return JwtEncrypt
     */
    public function setKey($keyMaterial, $algorithm = 'HS256')
    {
        $this->key = new Key($keyMaterial, $algorithm);
        return $this;
    }

    /**
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
    }


    /**
     * @param array $payload
     * @return string
     */
    public function encode(array $payload)
    {
        return JWT::encode($payload, $this->getKey()->getKeyMaterial(), $this->getKey()->getAlgorithm());
    }

    /**
     * @param string $token
     * @return object
     */
    public function decode($token)
    {
        return JWT::decode($token, $this->getKey());
    }
}