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

namespace tp5er\think\auth\casbin\adapters;

use Casbin\Persist\Adapter as AdapterContract;
use Casbin\Persist\AdapterHelper;
use tp5er\think\auth\casbin\AppService;

class ModelAdapter implements AdapterContract
{
    use AdapterHelper;

    /**
     * @var CasbinRule
     */
    private $casbinRule;

    public function __construct()
    {
        $model = AppService::getCfg("rule_model");
        $this->casbinRule = new $model;
    }

    /**
     * @param $ptype
     * @param array $rule
     *
     * @return void
     */
    public function savePolicyLine($ptype, array $rule)
    {
        $col['ptype'] = $ptype;
        foreach ($rule as $key => $value) {
            $col['v' . strval($key) . ''] = $value;
        }
        $this->casbinRule->create($col);
    }

    /**
     * @param $model
     *
     * @return void
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function loadPolicy($model)
    {
        $rows = $this->casbinRule->select()->toArray();
        foreach ($rows as $row) {
            if (is_object($row) && method_exists($row, 'toArray')) {
                $row = $row->toArray();
            }

            $line = implode(', ', array_filter(array_slice($row, 1), function ($val) {
                return '' != $val && ! is_null($val);
            }));
            $this->loadPolicyLine(trim($line), $model);
        }
    }

    /**
     * @param $model
     *
     * @return true
     */
    public function savePolicy($model)
    {
        foreach ($model->model['p'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }
        foreach ($model->model['g'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }

        return true;
    }

    /**
     * @param $sec
     * @param $ptype
     * @param $rule
     *
     * @return void
     */
    public function addPolicy($sec, $ptype, $rule)
    {
        $this->savePolicyLine($ptype, $rule);
    }

    /**
     * @param $sec
     * @param $ptype
     * @param $rule
     *
     * @return bool|mixed
     */
    public function removePolicy($sec, $ptype, $rule)
    {
        $result = $this->casbinRule->where('ptype', $ptype);
        foreach ($rule as $key => $value) {
            $result->where('v' . strval($key), $value);
        }

        return $result->delete();
    }

    /**
     * @param $sec
     * @param $ptype
     * @param $fieldIndex
     * @param ...$fieldValues
     *
     * @return int
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues)
    {
        $count = 0;
        $instance = $this->casbinRule->where('ptype', $ptype);
        foreach (range(0, 5) as $value) {
            if ($fieldIndex <= $value && $value < $fieldIndex + count($fieldValues)) {
                if ('' != $fieldValues[$value - $fieldIndex]) {
                    $instance->where('v' . strval($value), $fieldValues[$value - $fieldIndex]);
                }
            }
        }
        foreach ($instance->select() as $model) {
            if ($model->delete()) {
                ++$count;
            }
        }

        return $count;
    }
}
