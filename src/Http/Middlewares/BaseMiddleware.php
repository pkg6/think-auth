<?php

namespace tp5er\think\auth\Http\Middlewares;

use Closure;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use tp5er\think\auth\JwtAuthManager;
use tp5er\think\auth\Traits\RequestToken;
use tp5er\think\auth\Traits\ResponseData;

/**
 * Class BaseMiddleware
 * @package tp5er\think\auth\Http\Middlewares
 */
abstract class BaseMiddleware
{
    use RequestToken, ResponseData;

    /**
     * @return JwtAuthManager
     */
    abstract public function gruard();

    /**
     * @param $request
     * @param Closure $next
     * @return mixed|\think\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function check($request, Closure $next)
    {
        try {
            if ($this->gruard()->authenticate()) {
                return $next($request);
            }
            return $this->response('402', '未登录', []);
        } catch (BeforeValidException $e) {
            return $this->response('402', '已经过期', []);
        } catch (SignatureInvalidException $e) {
            return $this->response('402', '签名失败', []);
        } catch (ExpiredException $e) {
            return $this->response('402', '已过期', []);
        } catch (\Exception $e) {
            return $this->response('402', $e->getMessage(), []);
        }
    }

    /**
     * @param $request
     * @param Closure $next
     * @return mixed|\think\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function refreshToken($request, Closure $next)
    {
        try {
            if ($this->gruard()->authenticate()) {
                return $next($request);
            }
            return $this->response('402', '未登录', []);
        } catch (ExpiredException $e) {
            $token = $this->gruard()->refresh();
            $next($request)->header([
                'Authorization' => 'Bearer ' . $token
            ]);
        } catch (\Exception $e) {
            return $this->response('402', $e->getMessage(), []);
        }
    }
}