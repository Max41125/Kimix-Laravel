<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
       'https://max41125-kimix-next-5015.twc1.net/', // ваш продакшн домен
        env('LOCAL_URL'),    // ваш локальный домен для разработки
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];

