<?php

namespace tp5er\think\auth\Http\Controller;

use Psr\SimpleCache\InvalidArgumentException;
use think\Request;
use think\Response;
use tp5er\think\auth\Events\Registered;
use tp5er\think\auth\Http\Validates\UserValidate;
use tp5er\think\auth\JwtAuthManager;
use tp5er\think\auth\Traits\ResponseData;
use tp5er\think\auth\Traits\ThinkValidate;

/**
 * Class JwtBaseController
 * @package tp5er\think\auth\Http\Controller
 */
abstract class JwtBaseController
{
    use ResponseData, ThinkValidate;

    /**
     * app()->get('auth.jwt')->setAuth('web');
     * @return JwtAuthManager
     */
    abstract public function gruard();

    // 成功之后的操作

    /**
     *
     */
    public function afterLogin()
    {

    }

    /**
     * 登陆
     * @param Request $request
     * @return Response
     * @throws InvalidArgumentException
     * @throws \tp5er\think\auth\Exceptions\LoginException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);
        $token = $this->gruard()->attempt($request->only(['name', 'password']));
        $this->afterLogin();
        return $this->success([
            'token'  => 'Bearer ' . $token,
            'header' => 'Authorization',
        ]);
    }

    /**
     * 验证登陆信息
     * @param Request $request
     */
    public function validateLogin(Request $request)
    {
        $this->validate($request->param(), UserValidate::class, 'login');
    }

    /**
     * @param Request $request
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \tp5er\think\auth\Exceptions\LoginException
     */
    public function register(Request $request)
    {
        $this->validatorRegister($request);

        event(new Registered($user = $this->create($request->param())));

        return $this->gruard()->attempt([
            'name'     => $request->param('name'),
            'password' => $request->param('password')
        ]);
    }

    /**
     * @param $data
     * @return mixed
     */
    abstract public function create($data);

    /**
     * @param Request $request
     */
    public function validatorRegister(Request $request)
    {
        $this->validate($request->param(), UserValidate::class, 'register');
    }


    /**
     * 刷新token
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function resetToken()
    {
        $token = $this->gruard()->refresh();
        return $this->success([
            'token'  => 'Bearer ' . $token,
            'header' => 'Authorization',
        ]);
    }

    /**
     * 退出登陆
     * @throws InvalidArgumentException
     */
    public function logout()
    {
        $this->gruard()->logout();
        return $this->success([], '退出登陆成功');
    }

    /**
     * 获取用户信息
     * @return Response
     * @throws InvalidArgumentException
     */
    public function info()
    {
        $info = $this->gruard()->authenticate();
        return $this->success($info->toArray());
    }
}