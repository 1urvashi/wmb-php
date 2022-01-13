<?php

return [
    /*
      |--------------------------------------------------------------------------
      | Third Party Services
      |--------------------------------------------------------------------------
      |
      | This file is for storing the credentials for third party services such
      | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
      | default location for this type of information, allowing packages
      | to have a conventional place to find your various credentials.
      |
     */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],
    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],
    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],
    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'firebase' => [
        /* 'api_key' => 'AIzaSyCD18sYCXO0x4eCCOQsRF6308hA7qdOfJ4', // Only used for JS integration
          'auth_domain' => 'laravel-f0881.firebaseapp.com', // Only used for JS integration
          'database_url' => 'https://laravel-f0881.firebaseio.com',
          'secret' => 'GsuJG7p2CmEA4dML8jZOTYmQi2XruOHapjsAGlyK',
          'storage_bucket' => 'laravel-f0881.appspot.com', */
        //staging
        /* 'api_key' => 'AIzaSyBO-YMk6tMZ2O0cQGzpjRlS1vT7gquAa40',
          'auth_domain' => 'wecashanycartest.firebaseapp.com',
          'database_url' => 'https://wecashanycartest.firebaseio.com',
          'secret' => 'mUHmSeLvM28fFkqoK4pb1jfzchDPdUEsXGDl9h9N',
          'storage_bucket' => 'wecashanycartest.appspot.com', */

        //live
        'api_key' => env('FIREBASE_API_KEY'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        'database_url' => env('FIREBASE_DATABASE_URL'),
        'secret' => env('FIREBASE_DATABASE_SECRET'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
    ],
    // 'twilio' => [
    //     'apiKey' => 'SK91cfd433444f0fe6459093dd74acbf97',
    //     'apiSecret' => 'hODeJRLzLT9Jr2HdNNxr41jr8lW0aCFb',
    //     'accountSid' => 'ACe8ad3adf427fc2c5586d6e1e77dc7f7c',
    //     'authToken' => '676c8c630834e2361e1397a1534a9655',
    //     'serviceSid' => (env('APP_ENV') == 'dev' || env('APP_ENV') == 'prod1') ? 'IS4ef2a536997205e84f6a0c0dacefa40e' : 'IS844495a29029acb886f197d4333b5ef3',
    //     'inpectorServiceSid' => (env('APP_ENV') == 'dev' || env('APP_ENV') == 'prod1') ? 'ISce6ac6d644fb737d8da0c7a01bf11426' : 'ISce6ac6d644fb737d8da0c7a01bf11426',
    // ]
    'twilio' => [
        'apiKey' => env('TWILIO_API_KEY'),
        'apiSecret' => env('TWILIO_API_SECRET'),
        'accountSid' => env('TWILIO_ACCOUNT_SID'),
        'authToken' => env('TWILIO_AUTH_TOKEN'),
        'serviceSid' => (env('APP_ENV') == 'dev' || env('APP_ENV') == 'production') 
		? env('SID') 
		: '',
    ]
];

