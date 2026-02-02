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
            'https://www.travelandleisure.com/feed',
            'https://www.nationalgeographic.com/content/natgeo/en_us/travel/_jcr_content/rss',
            'https://www.cntraveler.com/feed/rss',
            'https://www.thecut.com/rss/index.xml',
            'https://www.vanityfair.com/style/rss',
            'https://www.complex.com/feeds/rss/all',
            ],
        'x' => [
            'bearer_token' => env('X_BEARER_TOKEN', ''),
            'api_key' => env('X_API_KEY', ''),
            'api_key_secret' => env('X_API_KEY_SECRET', ''),
            'access_token' => env('X_ACCESS_TOKEN', ''),
            'access_token_secret' => env('X_ACCESS_TOKEN_SECRET', ''),
            'default_query' => env('NEWS_X_DEFAULT_QUERY', 'entertainment OR culture OR pop culture OR music OR film OR celebrity'),
        ],
        'default_query' => env('NEWS_DEFAULT_QUERY', 'fashion OR lifestyle OR entertainment OR culture OR travel'),
        'language' => env('NEWS_LANGUAGE', 'en'),
        'country' => env('NEWS_COUNTRY', 'us'),
        'max_results' => env('NEWS_MAX_RESULTS', 20),
        'max_age_days' => env('NEWS_MAX_AGE_DAYS', 21),
        'cache_minutes' => env('NEWS_CACHE_MINUTES', 60),
    ],

    'maps' => [
        'enabled' => env('MAPS_ENABLED', true),
        'provider' => env('MAPS_PROVIDER', 'osm'), // 'google' or 'osm'
    ],

    'indexnow' => [
        'key' => env('INDEXNOW_KEY', ''),
        'host' => env('INDEXNOW_HOST', ''),
        'key_location' => env('INDEXNOW_KEY_LOCATION', ''),
    ],

    'ai' => [
        'provider' => env('9YTTRYBE_AI_PROVIDER', 'auto'), // auto|openai|gemini
        'model' => env('9YTTRYBE_AI_MODEL', 'gpt-4o-mini'),
        'review_threshold' => env('9YTTRYBE_AI_REVIEW_THRESHOLD', 0.45),
        'async' => env('9YTTRYBE_AI_ASYNC', false),
        'insecure' => env('9YTTRYBE_AI_INSECURE', false),
        'api_base_url' => env('9YTTRYBE_AI_API_BASE_URL', 'https://api.openai.com'),
        'seo' => [
            'limit' => env('AI_SEO_DAILY_LIMIT', 80),
            'days' => env('AI_SEO_LOOKBACK_DAYS', 30),
            'only_missing' => env('AI_SEO_ONLY_MISSING', true),
            'types' => array_values(array_filter(array_map('trim', explode(',', env('AI_SEO_TYPES', 'events,polls,articles,companies,products,surveys,conferences,categories'))))),
            'languages' => array_values(array_filter(array_map('trim', explode(',', env('AI_SEO_LANGUAGES', 'en,fr,es,pt'))))),
        ],
        'blog' => [
            'daily_count' => env('AI_BLOG_DAILY_COUNT', 2),
            'auto_publish' => env('AI_BLOG_AUTO_PUBLISH', false),
            'how_to_topics' => array_values(array_filter(array_map('trim', explode('|', env('AI_BLOG_HOW_TO_TOPICS', 'How to sell more event tickets|How to promote your event with bulk SMS|How to create a high-converting event page|How to use polls to boost engagement|How to create surveys that get responses'))))),
            'whats_on_regions' => array_values(array_filter(array_map('trim', explode('|', env('AI_BLOG_WHATS_ON_REGIONS', 'Greater Accra|Ashanti|Central|Western|Eastern|Volta'))))),
        ],
        'search' => [
            'enabled' => env('AI_SEARCH_ENABLED', true),
            'min_len' => env('AI_SEARCH_MIN_LEN', 3),
            'max_synonyms' => env('AI_SEARCH_MAX_SYNONYMS', 6),
            'cache_minutes' => env('AI_SEARCH_CACHE_MINUTES', 1440),
        ],
        'enrichment' => [
            'limit' => env('AI_ENRICH_DAILY_LIMIT', 40),
            'faq_count' => env('AI_FAQ_COUNT', 5),
            'tag_count' => env('AI_TAG_COUNT', 10),
        ],
        'growth' => [
            'digest_day' => env('AI_GROWTH_DIGEST_DAY', 1), // 1 = Monday
            'organizer_limit' => env('AI_GROWTH_ORGANIZER_LIMIT', 20),
            'social_limit' => env('AI_GROWTH_SOCIAL_LIMIT', 30),
        ],
        'landing' => [
            'cache_days' => env('AI_LANDING_CACHE_DAYS', 7),
            'max_events' => env('AI_LANDING_MAX_EVENTS', 16),
        ],
        'translation' => [
            'cache_days' => env('AI_TRANSLATION_CACHE_DAYS', 30),
        ],
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),
        'api_base_url' => env('9YTTRYBE_AI_API_BASE_URL', 'https://api.openai.com'),
        'model' => env('9YTTRYBE_AI_MODEL', 'gpt-4o-mini'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'api_base_url' => env('GEMINI_API_BASE_URL', 'https://generativelanguage.googleapis.com'),
        'model' => env('9YTTRYBE_GEMINI_MODEL', 'gemini-1.5-flash'),
    ],

];
