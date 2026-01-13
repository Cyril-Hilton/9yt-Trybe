<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'url' => env('PAYSTACK_URL', 'https://api.paystack.co'),
    ],

    'sms' => [
        'default_provider' => env('SMS_DEFAULT_PROVIDER', 'mnotify'),
    ],

    'mnotify' => [
        'api_key' => env('MNOTIFY_API_KEY', ''),
        'sender_id' => env('MNOTIFY_SENDER_ID', 'MNOTIFY'),
    ],

    'hubtel' => [
        'client_id' => env('HUBTEL_CLIENT_ID', ''),
        'client_secret' => env('HUBTEL_CLIENT_SECRET', ''),
        'sender_id' => env('HUBTEL_SENDER_ID', ''),
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
    ],

    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect' => env('MICROSOFT_REDIRECT_URI', env('APP_URL') . '/auth/microsoft/callback'),
    ],

    'yahoo' => [
        'client_id' => env('YAHOO_CLIENT_ID'),
        'client_secret' => env('YAHOO_CLIENT_SECRET'),
        'redirect' => env('YAHOO_REDIRECT_URI', env('APP_URL') . '/auth/yahoo/callback'),
    ],

    'ip_geolocation' => [
        'api_key' => env('IP_GEOLOCATION_API_KEY', ''),
    ],

    'news' => [
        'provider' => env('NEWS_PROVIDER', 'gnews'),
        'gnews' => [
            'api_key' => env('GNEWS_API_KEY', ''),
            'base_url' => env('GNEWS_BASE_URL', 'https://gnews.io/api/v4'),
        ],
        'newsapi' => [
            'api_key' => env('NEWSAPI_KEY', ''),
            'base_url' => env('NEWSAPI_BASE_URL', 'https://newsapi.org/v2'),
        ],
        'rss_feeds' => [
            'https://www.vogue.com/rss',
            'https://www.gq.com/feed/rss',
            'https://www.vanityfair.com/feed/rss',
            'https://www.hypebeast.com/feed',
            'https://www.elle.com/rss/all.xml',
            'https://www.harpersbazaar.com/rss/all.xml',
            'https://www.cosmopolitan.com/rss/all.xml',
            'https://www.esquire.com/rss/all.xml',
            'https://www.allure.com/feed/rss',
            'https://www.refinery29.com/en-us/rss.xml',
            'https://www.rollingstone.com/culture/feed/',
            'https://people.com/feed/',
            'https://www.buzzfeed.com/celebrity.xml',
            'https://www.buzzfeed.com/lol.xml',
            'https://www.travelandleisure.com/feed',
            'https://www.nationalgeographic.com/content/natgeo/en_us/travel/_jcr_content/rss',
            'https://www.cntraveler.com/feed/rss',
            'https://www.thecut.com/rss/index.xml',
            'https://www.vanityfair.com/style/rss',
            'https://www.complex.com/feeds/rss/all',
        ],
        'default_query' => env('NEWS_DEFAULT_QUERY', 'fashion OR lifestyle OR entertainment OR culture OR travel'),
        'language' => env('NEWS_LANGUAGE', 'en'),
        'country' => env('NEWS_COUNTRY', 'us'),
        'max_results' => env('NEWS_MAX_RESULTS', 20),
        'cache_minutes' => env('NEWS_CACHE_MINUTES', 60),
    ],

];
