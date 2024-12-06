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

use tp5er\think\auth\jwt\claims\Factory as ClaimFactory;
use tp5er\think\auth\jwt\contracts\JWT;
use tp5er\think\auth\jwt\contracts\Storage;
use tp5er\think\auth\jwt\providers\jwt\Lcobucci;
use tp5er\think\auth\jwt\providers\storage\Think;
use tp5er\think\auth\jwt\validators\PayloadValidator;

class AppService extends \tp5er\think\auth\AppService
{
    const claimFactory = ClaimFactory::class;
    const claimsValidatorFactory = ClaimsValidatorFactory::class;

    const storge = Storage::class;
    const validatorpayload = "tp5er.auth.jwt.validators.payload";
    const blacklist = "tp5er.auth.jwt.blacklist";
    const cipher = JWT::class;
    const manager = 'tp5er.auth.jwt.manager';
    const auth = 'tp5er.auth.jwt.auth';

    public $config = [
        'secret' => '',
        'algo' => 'HS256',
        'keys' => [
            'public' => '',
            'private' => '',
            'passphrase' => '',
        ],
        'ttl' => 60,
        'leeway' => 0,
        'blacklist_enabled' => true,
        'refresh_ttl' => 20160,
        'required_claims' => [
            'iss',
            'iat',
            'exp',
            'nbf',
            'sub',
            'jti',
        ],
        'blacklist_grace_period' => 0,
        'persistent_claims' => [
            // 'foo',
            // 'bar',
        ],
        'lock_subject' => true,
        'providers' => [
            'jwt' => \tp5er\think\auth\jwt\providers\jwt\Lcobucci::class,
            "storage" => \tp5er\think\auth\jwt\providers\storage\Think::class,
        ]
    ];
    public static function name()
    {
        return 'jwt';
    }

    public function bind()
    {
        $this->app->bind(AppService::claimFactory, function () {
            return new ClaimFactory($this->app->request);
        });
        $this->app->bind(AppService::validatorpayload, function () {
            return (new PayloadValidator)
                ->setRefreshTTL(self::getConfig('refresh_ttl'))
                ->setRequiredClaims(self::getConfig('required_claims', []));
        });

        $this->app->bind(AppService::claimsValidatorFactory, function () {
            $factory = new ClaimsValidatorFactory(
                $this->app->get(AppService::claimFactory),
                $this->app->get(AppService::validatorpayload)
            );

            return $factory->setTTL(self::getConfig('ttl'))
                ->setLeeway(self::getConfig('leeway'));
        });

        $this->app->bind(AppService::storge, function () {
            $storageClass = self::getConfig('providers.storage', Think::class);

            return new $storageClass($this->app);
        });

        $this->app->bind(AppService::blacklist, function () {
            $instance = new Blacklist($this->app->get(AppService::storge));

            return $instance->setGracePeriod(self::getConfig('blacklist_grace_period'))
                ->setRefreshTTL(self::getConfig('refresh_ttl'));
        });

        $this->app->bind(AppService::cipher, function () {
            $jwtClass = self::getConfig('providers.jwt', Lcobucci::class);

            return new $jwtClass(
                self::getConfig('secret'),
                self::getConfig('algo'),
                self::getConfig('keys')
            );
        });

        $this->app->bind(AppService::manager, function () {
            $instance = new Manager(
                $this->app->get(AppService::cipher),
                $this->app->get(AppService::blacklist),
                $this->app->get(AppService::claimsValidatorFactory)
            );

            return $instance
                ->setBlacklistEnabled((bool) self::getConfig('blacklist_enabled', true))
                ->setPersistentClaims(self::getConfig('persistent_claims', []));
        });
        $this->app->bind(AppService::auth, function () {
            $instance = new JWTAuth(
                $this->app->get(AppService::manager),
                $this->app->get(\tp5er\think\auth\keyparser\AppService::keyParser)
            );

            return $instance->lockSubject(self::getConfig('lock_subject'));
        });
    }
}
