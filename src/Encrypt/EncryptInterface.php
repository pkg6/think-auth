<?php

namespace tp5er\think\auth\Encrypt;

/**
 * Interface EncryptInterface
 * @package tp5er\think\auth\Encrypt
 */
interface EncryptInterface
{
    /**
     * @param  array  $payload
     *
     * @return string
     */
    public function encode(array $payload);

    /**
     * @param  string  $token
     *
     * @return array
     */
    public function decode($token);
}