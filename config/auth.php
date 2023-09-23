<?php


return [
    //身份验证默认值
    'default' => 'web',
    /*
     * 身份验证卫士
     * 驱动 支持 session
     * 用户提供者
     * 驱动支持 database model
     * */
    'guards'  => [
        'web'   => [
            'driver'   => 'session',
            'provider' => [
                'driver' => 'database',
                'table'  => 'users',
            ],
        ],
        'model' => [
            'driver'   => 'session',
            'provider' => [
                'driver' => 'model',
                'model'  => \tp5er\think\auth\Model\User::class,
            ],
        ],
    ],
    //参考
    //https://github.com/firebase/php-jwt
    'jwt'     => [
        'keyMaterial' => 'tp5er_key_material',
        'algorithm'   => 'HS256',
        'leeway'      => 60,
    ]
];
