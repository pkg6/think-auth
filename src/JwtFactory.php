<?php

namespace tp5er\think\auth;

use InvalidArgumentException;
use think\App;
use tp5er\think\auth\Encrypt\EncryptInterface;
use tp5er\think\auth\Encrypt\JwtEncrypt;
use tp5er\think\auth\Support\Payload;


class JwtFactory
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var int
     */
    protected $exp;
    /**
     * @var EncryptInterface
     */
    private $encrypt;
    /**
     * @var string
     */
    private $keyMaterial;
    /**
     * @var string
     */
    private $algorithm;
    /**
     * @var int
     */
    private $leeway;

    /**
     * @var int
     */
    protected $sub;

    /**
     * JwtManager constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app         = $app;
        $this->encrypt     = new JwtEncrypt();
        $this->keyMaterial = $this->app->config->get('auth.jwt.keyMaterial', 'tp5er_key_material');
        $this->algorithm   = $this->app->config->get('auth.jwt.algorithm', 'HS256');
        $this->leeway      = $this->app->config->get('auth.jwt.leeway', 60);
        $this->exp         = $this->app->config->get('auth.jwt.exp', 3);
        $this->encrypt->setKey($this->keyMaterial, $this->algorithm)->setLeeway($this->leeway);
    }

    /**
     * @param int $sub
     */
    public function setSub(int $sub): void
    {
        $this->sub = $sub;
    }

    /**
     * @return int
     */
    public function getSub(): int
    {
        return $this->sub;
    }

    /**
     * @param string $token
     * @return Payload
     */
    public function getPayload(string $token)
    {
        return new Payload($this->encrypt->decode($token));
    }

    /**
     * @param array $payload
     * @return string
     */
    public function buildToken($payload = [])
    {

        $nowTime = time();
        $payload = array_merge([
            'iss' => $this->app->request->domain(),
            'iat' => $nowTime,
            'nbf' => $nowTime,
            'exp' => strtotime('+' . $this->exp . ' minutes', $nowTime),
            'jti' => hash('sha256', uniqid('JWT') . time()),
        ], $payload);
        if (empty($payload['sub'])) {
            throw new InvalidArgumentException('no setting sub');
        }
        $this->setSub($payload['sub']);
        return $this->encrypt->encode($payload);
    }

    /**
     * @param $oldToken
     * @return string
     */
    public function refresh($oldToken)
    {
        $oldPayload = $this->getPayload($oldToken);
        $nowTime    = time();
        $payload    = array_merge($oldPayload->all(), [
            'wt'  => $oldToken,
            'iat' => $nowTime,
            'nbf' => $nowTime,
            'exp' => strtotime('+' . $this->exp . ' minutes', $nowTime),
        ]);
        if (empty($payload['sub'])) {
            throw new InvalidArgumentException('no setting sub');
        }
        $this->setSub($payload['sub']);
        return $this->encrypt->encode($payload);
    }
}