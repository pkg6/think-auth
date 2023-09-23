<?php

namespace tp5er\think\auth\Http\Middlewares;

use Closure;
use think\Config;
use think\Request;
use think\Response;

/**
 * Class AllowCrossDomain
 * @package tp5er\think\auth\Http\Middlewares
 */
class AllowCrossDomain
{
    /**
     * @var mixed
     */
    protected $cookieDomain;

    /**
     * @var array
     */
    protected $header = [
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Max-Age'           => 1800,
        'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since,X-Token,X-CSRF-TOKEN,X-Requested-With',
    ];

    /**
     * AllowCrossDomain constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->cookieDomain = $config->get('cookie.domain', '');
    }

    /**
     * 允许跨域请求
     * @access public
     * @param Request $request
     * @param Closure $next
     * @param array $header
     * @return Response
     */
    public function handle($request, Closure $next, ? array $header = [])
    {
        $header = !empty($header) ? array_merge($this->header, $header) : $this->header;
        if (!isset($header['Access-Control-Allow-Origin'])) {
            $origin = $request->header('origin');

            if ($origin && ('' == $this->cookieDomain || strpos($origin, $this->cookieDomain))) {
                $header['Access-Control-Allow-Origin'] = $origin;
            } else {
                $header['Access-Control-Allow-Origin'] = '*';
            }
        }
        return $next($request)->header($header);
    }
}