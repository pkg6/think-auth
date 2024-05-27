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

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',

        //The provider generally does not need to be set up, but can be mined by oneself
        //"provider" => "user"
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Remember that the password validity time can be set to remember
    | If your driver is a session, you can also set it using setRememberDuration
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'user',
            // Set the membership validity period to 24 hours
            //"remember" => 24 * 3600
        ],
        "token" => [
            'driver' => 'token',
            'provider' => 'user',
        ],
        'jwt' => [
            'driver' => 'jwt',
            'provider' => 'user',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent","model"
    |
    */

    'providers' => [
        'user' => [
            'driver' => 'model', //eloquent
            'model' => \tp5er\think\auth\User::class,
        ],
        //'user' => [
        //    'driver' => 'database',
        //    'table' => 'user',
        //],
    ],

    "policies" => [
        //'app\model\Model' => 'app\policies\ModelPolicy',
    ],

    "sanctum" => [

        /*
        |--------------------------------------------------------------------------
        | Sanctum Guards
        |--------------------------------------------------------------------------
        |
        | This array contains the authentication guards that will be checked when
        | Sanctum is trying to authenticate a request. If none of these guards
        | are able to authenticate the request, Sanctum will use the bearer
        | token that's present on an incoming request for authentication.
        |
        */
        'guard' => ['web'],

        /*
        |--------------------------------------------------------------------------
        | Expiration Minutes
        |--------------------------------------------------------------------------
        |
        | This value controls the number of minutes until an issued token will be
        | considered expired. If this value is null, personal access tokens do
        | not expire. This won't tweak the lifetime of first-party sessions.
        |
        */
        'expiration' => null,

    ],

    "jwt" => [
        /*
         |--------------------------------------------------------------------------
         | JWT Authentication Secret
         |--------------------------------------------------------------------------
         |
         | Don't forget to set this in your .env file, as it will be used to sign
         | your tokens : Generate 64 random strings through Str::random(64)
         |
         | Note: This will be used for Symmetric algorithms only (HMAC),
         | since RSA and ECDSA use a private/public key combo (See below).
         |
         */
        'secret' => env('JWT_SECRET'),
        'algo' => env('JWT_ALGO', 'HS256'),
        'keys' => [
            /*
            |--------------------------------------------------------------------------
            | Public Key
            |--------------------------------------------------------------------------
            |
            | A path or resource to your public key.
            |
            | E.g. 'file://path/to/public/key'
            |
            */

            'public' => env('JWT_PUBLIC_KEY'),

            /*
            |--------------------------------------------------------------------------
            | Private Key
            |--------------------------------------------------------------------------
            |
            | A path or resource to your private key.
            |
            | E.g. 'file://path/to/private/key'
            |
            */

            'private' => env('JWT_PRIVATE_KEY'),

            /*
            |--------------------------------------------------------------------------
            | Passphrase
            |--------------------------------------------------------------------------
            |
            | The passphrase for your private key. Can be null if none set.
            |
            */

            'passphrase' => env('JWT_PASSPHRASE'),
        ],
        'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),
        'persistent_claims' => [
            // 'foo',
            // 'bar',
        ],
        'providers' => [
            'jwt' => \tp5er\think\auth\jwt\providers\jwt\Lcobucci::class,
            "storage" => \tp5er\think\auth\jwt\providers\storage\Think::class,
        ]
    ],

    'middleware' => [
        'global' => [
            \think\middleware\SessionInit::class,
        ],
        "alias" => [
            'auth' => \tp5er\think\auth\middlewares\Authenticate::class,
            'auth.basic' => \tp5er\think\auth\middlewares\AuthenticateWithBasicAuth::class,
            'can' => \tp5er\think\auth\middlewares\Authorize::class,

            //sanctum
            'abilities' => \tp5er\think\auth\sanctum\middlewares\CheckAbilities::class,
            'ability' => \tp5er\think\auth\sanctum\middlewares\CheckForAnyAbility::class,
        ],
    ],
];
