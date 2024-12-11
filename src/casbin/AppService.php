<?php

namespace tp5er\think\auth\casbin;

use Casbin\Model\Model;
use Casbin\Enforcer;


class AppService extends \tp5er\think\auth\AppService
{
    public $name = 'auth.casbin';

    const abstract_casbin = Model::class;

    public $config = [
        'model_conf_file' => '',
        'adapter' => \tp5er\think\auth\casbin\adapters\ModelAdapter::class,
        "rule_model" => \tp5er\think\auth\casbin\adapters\CasbinRule::class,
    ];

    public function bind()
    {
        $this->app->bind(AppService::abstract_casbin, function () {
            $model = new Model();
            $model->loadModel(self::getConfig('model_conf_file', root_path() . 'casbin-basic-model.conf'));
            $adapter = self::getConfig('adapter', \tp5er\think\auth\casbin\adapters\ModelAdapter::class);
            if (is_string($adapter) && class_exists($adapter)) {
                $adapter = new $adapter();
            }
            return new Enforcer($model, $adapter);
        });
    }
}