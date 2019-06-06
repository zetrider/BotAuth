<?php

return [

    'vkontakte' => [
        'link' => 'https://vk.me/...',
        'api_secret'  => env('BOTAUTH_VKONTAKTE_API_SECRET', ''),
        'api_token'   => env('BOTAUTH_VKONTAKTE_API_TOKEN', ''),
        'api_confirm' => env('BOTAUTH_VKONTAKTE_API_CONFIRM', ''),
        'api_user_fields' => [
            'id',
            'first_name',
            'last_name',
            'nickname',
            'screen_name',
            'photo_max_orig',
            'city',
            'country',
            'counters',
        ],
    ],
    'telegram' => [
        'link' => 'https://t.me/...',
        'api_token' => env('BOTAUTH_TELEGRAM_API_TOKEN', ''),
        'proxy' => env('BOTAUTH_TELEGRAM_PROXY', ''),
    ],
    'facebook' => [
        'link' => 'https://m.me/...',
        'api_secret'  => env('BOTAUTH_FACEBOOK_API_SECRET', ''),
        'api_token'   => env('BOTAUTH_FACEBOOK_API_TOKEN', ''),
        'api_confirm' => env('BOTAUTH_FACEBOOK_API_CONFIRM', ''),
        'api_user_fields' => [
            'id',
            'name',
            'first_name',
            'last_name',
            'profile_pic',
            'locale',
            'timezone',
            'gender',
        ],
    ],

];
