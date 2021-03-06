<?php

return [
    'adminEmail' => 'admin@example.com',

    'session_redis' => [
        'class'      => \yii\redis\Session::className(),
        'redis'      => [
            'hostname' => 'localhost',
            'port'     => 6379,
            'database' => 0,
        ],
        'keyPrefix'  => 'global:session:',
        'timeout'    => 3600 * 24,
        'useCookies' => true,
    ],
];
