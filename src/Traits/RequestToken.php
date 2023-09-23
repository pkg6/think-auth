<?php

namespace tp5er\think\auth\Traits;

use think\helper\Str;


trait RequestToken
{
    /**
     * @var \think\Request
     */
    protected $request;
    /**
     * @var string
     */
    protected $inputKey = 'token';

    /**
     * @return string
     */
    public function bearerToken()
    {
        $header = $this->getRequest()->header('Authorization', '');
        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }
    }

    /**
     * vue-element-admin
     * @return array|string
     */
    public function xToken()
    {
        return $this->getRequest()->header('X-Token');
    }

    /**
     * @return $this
     */
    public function parseToken()
    {
        $token = $this->getRequest()->param($this->inputKey, '');
        if (empty($token)) {
            $token = $this->bearerToken();
        }
        if (empty($token)) {
            $token = $this->request->header('PHP_AUTH_PW', '');
        }
        if (empty($token)) {
            $token = $this->xToken();
        }
        $this->token = $token;
        return $this;
    }

    /**
     * @return \think\Request
     */
    public function getRequest()
    {
        if (!is_null($this->request)) {
            return $this->request;
        }
        return $this->app->request ?? app()->request;
    }
}