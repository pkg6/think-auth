<?php

namespace tp5er\think\auth\Traits;

use think\exception\HttpResponseException;
use think\facade\View;
use think\Response;

trait ResponseData
{
    /**
     * 失败状态状态吗
     * @var int
     */
    protected $errorCode = 0;

    /**
     * 成功状态吗
     * @var int
     */
    protected $successCode = 1;

    /**
     * 返回数据格式 json xml
     * @var string
     */
    protected $respFormat = 'json';

    /**
     * 返回携带header头
     * @var array
     */
    protected $header = [];

    /**
     * 失败返回
     * @param string $msg
     * @return Response
     */
    public function error(string $msg = 'error')
    {
        return $this->response($this->errorCode, $msg, []);
    }

    /**
     * 成功返回
     * @param array $data
     * @param string $msg
     * @return Response
     */
    public function success(array $data, string $msg = 'success')
    {
        return $this->response($this->successCode, $msg, $data);
    }

    /**
     * 数据返回
     * @param int $code
     * @param string $msg
     * @param array $data
     * @param string $format
     * @param array $header
     * @return Response
     */
    public function response(int $code, string $msg, array $data, string $format = 'json', array $header = [])
    {
        $header = array_merge($this->header, $header);
        return Response::create(
            compact("code", "msg", "data"),
            $format ?? $this->respFormat
        )->header($header);
    }

    /**
     * 渲染模版
     * @param string $template
     * @param array $vars
     * @return string
     */
    public function fetch(string $template = '', array $vars = [])
    {
        return View::fetch($template, $vars);
    }

    /**
     * 模版渲染值
     * @param $name
     * @param null $value
     */
    public function assign($name, $value = null)
    {
        View::assign($name, $value);
    }

    /**
     * URL重定向
     * @param $url
     * @param int $code
     */
    public function redirect($url, int $code = 302)
    {
        $response = Response::create($url, 'redirect')->code($code);
        throw new HttpResponseException($response);
    }
}