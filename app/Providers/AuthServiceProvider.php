<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        // Passport::enablePasswordGrant();

        // Passport::tokensExpireIn(now()->addMinutes(config('auth.access_token_expiry', 15)));
        // Passport::refreshTokensExpireIn(now()->addDays(config('auth.refresh_token_expiry', 30)));
        // Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
