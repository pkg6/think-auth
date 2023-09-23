<?php

namespace tp5er\think\auth\Traits;

use think\exception\ValidateException;
use think\Validate;


trait ThinkValidate
{
    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param string|array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return bool
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, $message = null, bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : app()->parseClass('validate', $validate);

            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
            if (is_string($message) && empty($scene)) {
                $v->scene($message);
            }
        }
        if (is_array($message)) {
            $v->message($message);
        }
        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }
        return $v->failException(true)->check($data);
    }
}