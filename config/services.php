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

    'passport' => [
        /*
        |--------------------------------------------------------------------------
        | Passport Password Grant Client
        |--------------------------------------------------------------------------
        |
        | These credentials are used for OAuth2 password grant authentication.
        | When you run "php artisan passport:install", Passport creates a password
        | grant client and displays the client_id and client_secret.
        |
        | IMPORTANT: Store the PLAIN TEXT secret here (not hashed).
        | Passport will hash it when verifying against the database.
        |
        | MULTIPLE APPS SUPPORT:
        | You can create multiple clients for different apps (iOS, Android, Web, etc.):
        |
        | 1. Create clients manually:
        |    php artisan passport:client --password --name="iOS App"
        |    php artisan passport:client --password --name="Android App"
        |
        | 2. Store secrets in .env:
        |    PASSPORT_PASSWORD_CLIENT_SECRET=default_secret (for default client)
        |    PASSPORT_CLIENT_SECRET_{CLIENT_ID}=secret_for_ios_app
        |    PASSPORT_CLIENT_SECRET_{CLIENT_ID}=secret_for_android_app
        |
        | 3. Mobile apps can send client_id to identify themselves:
        |    { "email": "...", "password": "...", "client_id": "ios-app-client-id" }
        |
        | SECURITY:
        | - client_id can be sent by mobile app (public identifier)
        | - client_secret is NEVER sent by mobile app - server adds it automatically
        | - This allows identifying which app is calling while keeping secret secure
        |
        */
        'password_client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
        'password_client_secret' => env('PASSPORT_PASSWORD_CLIENT_SECRET'),
        
        /*
        |--------------------------------------------------------------------------
        | Allowed Redirect URIs for OAuth PKCE Flow
        |--------------------------------------------------------------------------
        |
        | These are the allowed redirect URIs for mobile apps using PKCE flow.
        | Add custom URIs in .env as comma-separated values:
        | PASSPORT_ALLOWED_REDIRECT_URIS=com.app://oauth,exp://oauth
        |
        */
        'allowed_redirect_uris' => array_filter(
            array_map('trim', explode(',', env('PASSPORT_ALLOWED_REDIRECT_URIS', '')))
        ),
    ],
];
