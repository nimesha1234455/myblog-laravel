<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | Application eke nama. Notification ekak wage, application eke nama
    | pennanna ona thenakadi framework eka me nama use karanawa.
    | .env eke APP_NAME eken thamai ganne.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | Application eka dan run wenne mona "environment" ekatada kiyala meken
    | kiyanawa (local / production wage). Services configure karana widiyatath
    | meka balapanawa. .env file eke set karanna.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | Debug mode eka on nam, error ekak awama stack trace ekka detailed
    | error message eka pennanawa. Off nam samanya error page ekak witharai
    | pennanne. Real server ekedi meka hamadama false karanna ona.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | Artisan command walin URL hadaddi me URL eka use karanawa.
    | Email eke "Read post" link eka wage ewath hadenne meken.
    | Meka application eke root URL ekata set karanna (ex: http://127.0.0.1:8000).
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Application ekata default timezone eka. PHP eke date / time functions
    | walata meka use wenawa. Default eka "UTC" ekak.
    |
    */

    'timezone' => 'asia/colombo',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | Application eke default bhashawa (locale) eka. Translation / localization
    | methods walata meka use wenawa. Translation strings thiyena ona
    | locale ekakata meka set karanna puluwan.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | Laravel eke encryption services me key eka use karanawa. Meka random
    | akuru 32 k wenna ona. Application eka deploy karanna kalin meka set
    | karanna ona. (Ex: SMTP password eka database eke encrypt karanne meken.)
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | Laravel eke "maintenance mode" eka manage karanna use karana driver eka.
    | "cache" driver eken maintenance mode eka server godak athare control
    | karanna puluwan.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];