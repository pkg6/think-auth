<?php

namespace tp5er\think\auth\Http\Handles;

use InvalidArgumentException;
use think\db\exception\DbException;
use think\exception\FuncNotFoundException;
use think\exception\Handle;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\ClassNotFoundException;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\RouteNotFoundException;
use think\exception\ValidateException;
use think\Response;
use Throwable;
use tp5er\think\auth\Traits\ResponseData;

/**
 * Class JwtApiHandle
 * @package tp5er\think\auth\Http\Handles
 */
class ApiHandle extends Handle
{
    use ResponseData;
    /**
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * JwtApiHandle constructor.
     */
    public function __construct()
    {
        parent::__construct(\app());
    }


    /**
     * @param \think\Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        //thinkphp参数校验失败
        if ($e instanceof ValidateException) {
            return $this->response(422, $e->getError(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        // 找不到资源报错
        if ($e instanceof ClassNotFoundException
            || $e instanceof RouteNotFoundException
            || $e instanceof FuncNotFoundException
            || ($e instanceof HttpException && $e->getStatusCode() == 404)
        ) {
            return $this->response(404, '当前请求资源不存在，请稍后再试', [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        // 系统性异常
        if ($e instanceof InvalidArgumentException
            || $e instanceof \ParseError
            || $e instanceof \TypeError
        ) {
            return $this->response(500, '系统异常，请稍后再试', [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        // thinkphp db
        if ($e instanceof DbException) {
            return $this->response(500, "数据操作异常,请稍后再试", [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        //HTTP异常
        if ($e instanceof HttpException) {
            return $this->response(500, $e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        //默认返回
        return $this->response($e->getCode(), $e->getMessage(), [
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}