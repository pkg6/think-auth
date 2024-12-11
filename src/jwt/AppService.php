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
    public $name = 'auth.jwt';
    const abstract_claim_factory = ClaimFactory::class;
    const abstract_claims_validator_factory = ClaimsValidatorFactory::class;

    const abstract_storge = Storage::class;
    const abstract_validatorpayload = "tp5er.auth.jwt.validators.payload";
    const abstract_blacklist = "tp5er.auth.jwt.blacklist";
    const abstract_cipher = JWT::class;
    const abstract_manager = 'tp5er.auth.jwt.manager';
    const abstract_auth = 'tp5er.auth.jwt.auth';

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

    public function bind()
    {
        $this->app->bind(AppService::abstract_claim_factory, function () {
            return new ClaimFactory($this->app->request);
        });
        $this->app->bind(AppService::abstract_validatorpayload, function () {
            return (new PayloadValidator)
                ->setRefreshTTL(self::getConfig('refresh_ttl'))
                ->setRequiredClaims(self::getConfig('required_claims', []));
        });

        $this->app->bind(AppService::abstract_claims_validator_factory, function () {
            $factory = new ClaimsValidatorFactory(
                $this->app->get(AppService::abstract_claim_factory),
                $this->app->get(AppService::abstract_validatorpayload)
            );

            return $factory->setTTL(self::getConfig('ttl'))
                ->setLeeway(self::getConfig('leeway'));
        });

        $this->app->bind(AppService::abstract_storge, function () {
            $storageClass = self::getConfig('providers.storage', Think::class);

            return new $storageClass($this->app);
        });

        $this->app->bind(AppService::abstract_blacklist, function () {
            $instance = new Blacklist($this->app->get(AppService::abstract_storge));

            return $instance->setGracePeriod(self::getConfig('blacklist_grace_period'))
                ->setRefreshTTL(self::getConfig('refresh_ttl'));
        });

        $this->app->bind(AppService::abstract_cipher, function () {
            $jwtClass = self::getConfig('providers.jwt', Lcobucci::class);

            return new $jwtClass(
                self::getConfig('secret'),
                self::getConfig('algo'),
                self::getConfig('keys')
            );
        });

        $this->app->bind(AppService::abstract_manager, function () {
            $instance = new Manager(
                $this->app->get(AppService::abstract_cipher),
                $this->app->get(AppService::abstract_blacklist),
                $this->app->get(AppService::abstract_claims_validator_factory)
            );

            return $instance
                ->setBlacklistEnabled((bool)self::getConfig('blacklist_enabled', true))
                ->setPersistentClaims(self::getConfig('persistent_claims', []));
        });
        $this->app->bind(AppService::abstract_auth, function () {
            $instance = new JWTAuth(
                $this->app->get(AppService::abstract_manager),
                $this->app->get(\tp5er\think\auth\keyparser\AppService::abstract_key_parser)
            );

            return $instance->lockSubject(self::getConfig('lock_subject'));
        });
    }
}
