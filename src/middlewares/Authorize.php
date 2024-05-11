<?php

/*
 * This file is part of the tp5er/think-auth
 *
 * (c) pkg6 <https://github.com/pkg6>
 *
 * (L) Licensed <https://opensource.org/license/MIT>
 *
 * (A) zhiqiang <https://www.zhiqiang.wang>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace tp5er\think\auth\middlewares;

use think\Model;
use think\Request;
use think\Response;
use tp5er\think\auth\access\Collection;
use tp5er\think\auth\contracts\GateInterface;

class Authorize
{
    /**
     * The gate instance.
     *
     * @var GateInterface
     */
    protected $gate;

    /**
     * Create a new middleware instance.
     *
     * @param GateInterface  $gate
     *
     * @return void
     */
    public function __construct(GateInterface $gate)
    {
        $this->gate = $gate;
    }
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @param null $guard
     * @param null $field
     *
     * @return Response
     */
    public function handle($request, \Closure $next, $ability, ...$models)
    {
        $this->gate->authorize($ability, $this->getGateArguments($request, $models));

        return $next($request);
    }
    /**
     * Get the arguments parameter for the gate.
     *
     * @param  Request  $request
     * @param  array|null  $models
     *
     * @return Model|array|string
     */
    protected function getGateArguments($request, $models)
    {
        if (is_null($models)) {
            return [];
        }

        return Collection::make($models)->map(function ($model) use ($request) {
            return $model instanceof Model ? $model : $this->getModel($request, $model);
        })->all();
    }

    /**
     * Get the model to authorize.
     *
     * @param  Request  $request
     * @param  string  $model
     *
     * @return Model|string
     */
    protected function getModel($request, $model)
    {
        if ($this->isClassName($model)) {
            return trim($model);
        } else {
            return $request->route($model, null) ?:
                ((preg_match("/^['\"](.*)['\"]$/", trim($model), $matches)) ? $matches[1] : null);
        }
    }
    /**
     * Checks if the given string looks like a fully qualified class name.
     *
     * @param  string  $value
     *
     * @return bool
     */
    protected function isClassName($value)
    {
        return strpos($value, '\\') !== false;
    }
}
