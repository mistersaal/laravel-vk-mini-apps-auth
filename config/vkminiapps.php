<?php
return [
    'app' => [
        'secret' => env('VK_SECRET'),
        'token' => env('VK_TOKEN'),
    ],
    'signUrl' => [
        'header' => env('VK_SIGN_HEADER', 'X-Vk-Auth-Url')
    ]
];
