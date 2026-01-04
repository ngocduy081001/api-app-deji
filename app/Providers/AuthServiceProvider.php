<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        // Enable password grant for mobile apps
        Passport::enablePasswordGrant();

        // Configure token expiration (optional - defaults are fine)
        // Passport::tokensExpireIn(now()->addMinutes(config('auth.access_token_expiry', 15)));
        // Passport::refreshTokensExpireIn(now()->addDays(config('auth.refresh_token_expiry', 30)));
        // Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // Ensure personal access client exists for 'customers' provider
        // This is required when using $user->createToken() method
        $this->ensurePersonalAccessClientExists();
    }

    /**
     * Ensure personal access client exists for customers provider
     * This is required when using $user->createToken() method
     */
    protected function ensurePersonalAccessClientExists(): void
    {
        try {
            // Check if personal access client exists for customers provider
            // Passport stores personal access client info in oauth_clients table
            $personalClient = Client::where('provider', 'customers')
                ->where('revoked', false)
                ->where(function ($query) {
                    // Check if it's a personal access client
                    // Personal access clients have 'personal_access' in grant_types
                    $query->whereJsonContains('grant_types', 'personal_access')
                        ->orWhere('name', 'like', '%Personal Access%');
                })
                ->first();

            if (!$personalClient) {
                // Create personal access client for customers provider
                $this->createPersonalAccessClient();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to ensure personal access client exists', [
                'error' => $e->getMessage(),
                'hint' => 'Run: php artisan passport:client --personal --provider=customers',
            ]);
        }
    }

    /**
     * Create personal access client for customers provider
     */
    protected function createPersonalAccessClient(): void
    {
        try {
            // Create personal access client directly
            // Personal access clients don't need secrets
            $client = Client::create([
                'name' => 'Personal Access Client - Customers',
                'secret' => null,
                'provider' => 'customers',
                'redirect_uris' => json_encode(['http://localhost']),
                'grant_types' => json_encode(['personal_access']),
                'revoked' => false,
            ]);

            Log::info('Personal access client created for customers provider', [
                'client_id' => $client->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create personal access client automatically', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Don't throw exception, just log - user can create manually
            Log::warning(
                'Personal access client not found for "customers" user provider. ' .
                    'Please run: php artisan passport:client --personal --provider=customers'
            );
        }
    }
}
