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

namespace tp5er\think\auth\jwt;

use think\App;
use think\helper\Arr;
use tp5er\think\auth\jwt\claims\Factory as ClaimFactory;
use tp5er\think\auth\jwt\contracts\JWT;
use tp5er\think\auth\jwt\contracts\Storage;
use tp5er\think\auth\jwt\providers\jwt\Lcobucci;
use tp5er\think\auth\jwt\providers\storage\Think;
use tp5er\think\auth\jwt\validators\PayloadValidator;

class Register
{
    const config = "jwt";

    const claimFactory = ClaimFactory::class;
    const claimsValidatorFactory = ClaimsValidatorFactory::class;

    const storge = Storage::class;
    const validatorpayload = "tp5er.auth.jwt.validators.payload";
    const blacklist = "tp5er.auth.jwt.blacklist";
    const cipher = JWT::class;
    const manager = 'tp5er.auth.jwt.manager';
    const auth = 'tp5er.auth.jwt.auth';

    public static function bind(App $app, $config)
    {
        $app->bind(Register::claimFactory, function () use (&$app) {
            return new ClaimFactory($app->request);
        });
        $app->bind(Register::validatorpayload, function () use (&$config) {
            return (new PayloadValidator)
                ->setRefreshTTL(Arr::get($config, 'refresh_ttl'))
                ->setRequiredClaims(Arr::get($config, 'required_claims', []));
        });

        $app->bind(Register::claimsValidatorFactory, function () use (&$app, &$config) {
            $factory = new ClaimsValidatorFactory(
                $app->get(Register::claimFactory),
                $app->get(Register::validatorpayload)
            );

            return $factory->setTTL(Arr::get($config, 'ttl'))
                ->setLeeway(Arr::get($config, 'leeway'));
        });

        $app->bind(Register::storge, function () use (&$app, $config) {
            $storageClass = Arr::get($config, 'providers.storage', Think::class);

            return new $storageClass($app);
        });

        $app->bind(Register::blacklist, function () use (&$app, &$config) {
            $instance = new Blacklist($app->get(Register::storge));

            return $instance->setGracePeriod(Arr::get($config, 'blacklist_grace_period'))
                ->setRefreshTTL(Arr::get($config, 'refresh_ttl'));
        });

        $app->bind(Register::cipher, function () use (&$config) {
            $jwtClass = Arr::get($config, 'providers.jwt', Lcobucci::class);

            return new $jwtClass(
                Arr::get($config, 'secret'),
                Arr::get($config, 'algo'),
                Arr::get($config, 'keys')
            );
        });

        $app->bind(Register::manager, function () use (&$app, $config) {
            $instance = new Manager(
                $app->get(Register::cipher),
                $app->get(Register::blacklist),
                $app->get(Register::claimsValidatorFactory)
            );

            return $instance
                ->setBlacklistEnabled((bool) Arr::get($config, 'blacklist_enabled', true))
                ->setPersistentClaims(Arr::get($config, 'persistent_claims', []));
        });
        $app->bind(Register::auth, function () use ($app, &$config) {
            $instance = new JWTAuth(
                $app->get(Register::manager),
                $app->get(\tp5er\think\auth\keyparser\Register::keyParser)
            );

            return $instance->lockSubject(Arr::get($config, 'lock_subject'));
        });
    }
}
