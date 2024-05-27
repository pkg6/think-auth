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
        /*
        |--------------------------------------------------------------------------
        | JWT time to live
        |--------------------------------------------------------------------------
        |
        | Specify the length of time (in minutes) that the token will be valid for.
        | Defaults to 1 hour.
        |
        | You can also set this to null, to yield a never expiring token.
        | Some people may want this behaviour for e.g. a mobile app.
        | This is not particularly recommended, so make sure you have appropriate
        | systems in place to revoke the token if necessary.
        | Notice: If you set this to null you should remove 'exp' element from 'required_claims' list.
        |
        */
        'ttl' => env('JWT_TTL', 60),
        /*
        |--------------------------------------------------------------------------
        | Leeway
        |--------------------------------------------------------------------------
        |
        | This property gives the jwt timestamp claims some "leeway".
        | Meaning that if you have any unavoidable slight clock skew on
        | any of your servers then this will afford you some level of cushioning.
        |
        | This applies to the claims `iat`, `nbf` and `exp`.
        |
        | Specify in seconds - only if you know you need it.
        |
        */
        'leeway' => env('JWT_LEEWAY', 0),

        /*
        |--------------------------------------------------------------------------
        | Blacklist Enabled
        |--------------------------------------------------------------------------
        |
        | In order to invalidate tokens, you must have the blacklist enabled.
        | If you do not want or need this functionality, then set this to false.
        |
        */
        'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),
        'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),
        /*
        |--------------------------------------------------------------------------
        | Required Claims
        |--------------------------------------------------------------------------
        |
        | Specify the required claims that must exist in any token.
        | A TokenInvalidException will be thrown if any of these claims are not
        | present in the payload.
        |
        */
        'required_claims' => [
            'iss',
            'iat',
            'exp',
            'nbf',
            'sub',
            'jti',
        ],

        /*
        | -------------------------------------------------------------------------
        | Blacklist Grace Period
        | -------------------------------------------------------------------------
        |
        | When multiple concurrent requests are made with the same JWT,
        | it is possible that some of them fail, due to token regeneration
        | on every request.
        |
        | Set grace period in seconds to prevent parallel request failure.
        |
        */
        'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

        /*
        |--------------------------------------------------------------------------
        | Persistent Claims
        |--------------------------------------------------------------------------
        |
        | Specify the claim keys to be persisted when refreshing a token.
        | `sub` and `iat` will automatically be persisted, in
        | addition to the these claims.
        |
        | Note: If a claim does not exist then it will be ignored.
        |
        */
        'persistent_claims' => [
            // 'foo',
            // 'bar',
        ],
        /*
        |--------------------------------------------------------------------------
        | Lock Subject
        |--------------------------------------------------------------------------
        |
        | This will determine whether a `prv` claim is automatically added to
        | the token. The purpose of this is to ensure that if you have multiple
        | authentication models e.g. `App\User` & `App\OtherPerson`, then we
        | should prevent one authentication request from impersonating another,
        | if 2 tokens happen to have the same id across the 2 different models.
        |
        | Under specific circumstances, you may want to disable this behaviour
        | e.g. if you only have one authentication model, then you would save
        | a little on token size.
        |
        */
        'lock_subject' => true,

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
