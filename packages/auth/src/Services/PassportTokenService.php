<?php

namespace Vendor\Auth\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Client;
use Laravel\Passport\RefreshToken as PassportRefreshToken;
use Laravel\Passport\Token as PassportToken;
use RuntimeException;

class PassportTokenService
{
    /**
     * Get password grant client from request, config, or database
     * 
     * SECURITY NOTE: 
     * - client_id can be sent by mobile app (it's public, not secret)
     * - client_secret is NEVER sent by mobile app - server adds it automatically
     * - This allows identifying which app is calling (iOS, Android, Web, etc.)
     * 
     * Priority:
     * 1. Use client_id from request (if provided and valid)
     * 2. Check config/services.php (if manually configured)
     * 3. Query database for first available password grant client
     * 
     * NOTE: client_secret must be PLAIN TEXT (not hashed)
     * Passport will verify it against the hashed secret stored in database.
     * 
     * @param string|null $clientId Optional client_id from request (to identify which app)
     * @return array ['id' => string, 'secret' => string, 'name' => string|null]
     */
    protected function getPasswordClient(?string $clientId = null): array
    {
        // If client_id provided from request, validate and use it
        if ($clientId) {
            $client = Client::where('id', $clientId)
                ->where('password_client', true)
                ->where('revoked', false)
                ->first();

            if (!$client) {
                throw new RuntimeException(
                    "Invalid client_id: {$clientId}. Client not found or not authorized for password grant."
                );
            }

            // Get secret from config (must be stored per client_id)
            $clientSecret = $this->getClientSecret($clientId, $client->name);

            return [
                'id' => (string) $client->id,
                'secret' => $clientSecret,
                'name' => $client->name,
            ];
        }

        // If no client_id from request, use default from config
        $configClientId = config('services.passport.password_client_id');
        $clientSecret = config('services.passport.password_client_secret');

        if ($configClientId && $clientSecret) {
            return [
                'id' => $configClientId,
                'secret' => $clientSecret,
                'name' => null,
            ];
        }

        // If not in config, try to get from database
        $client = Client::where('password_client', true)
            ->where('revoked', false)
            ->first();

        if ($client) {
            $dbClientId = (string) $client->id;
            $clientSecret = $this->getClientSecret($dbClientId, $client->name);

            return [
                'id' => $dbClientId,
                'secret' => $clientSecret,
                'name' => $client->name,
            ];
        }

        throw new RuntimeException(
            'Passport password client not found. ' .
                'Please run "php artisan passport:install" to create the password grant client, ' .
                'or create clients manually for each app (iOS, Android, etc.)'
        );
    }

    /**
     * Get client secret for a specific client_id
     * 
     * Supports multiple clients by storing secrets in config:
     * - PASSPORT_CLIENT_SECRET_{CLIENT_ID} for specific clients
     * - PASSPORT_PASSWORD_CLIENT_SECRET for default client
     * 
     * @param string $clientId
     * @param string|null $clientName
     * @return string
     */
    protected function getClientSecret(string $clientId, ?string $clientName = null): string
    {
        // Try to get secret for specific client_id
        $envKey = 'PASSPORT_CLIENT_SECRET_' . strtoupper(str_replace('-', '_', $clientId));
        $secret = env($envKey);

        if ($secret) {
            return $secret;
        }

        // Try to get secret by client name (if configured)
        if ($clientName) {
            $nameKey = 'PASSPORT_CLIENT_SECRET_' . strtoupper(str_replace([' ', '-'], '_', $clientName));
            $secret = env($nameKey);

            if ($secret) {
                return $secret;
            }
        }

        // Fallback to default password client secret
        $defaultSecret = config('services.passport.password_client_secret');

        if ($defaultSecret) {
            return $defaultSecret;
        }

        throw new RuntimeException(
            "Client secret not found for client_id: {$clientId}. " .
                "Please set PASSPORT_CLIENT_SECRET_{$clientId} or PASSPORT_PASSWORD_CLIENT_SECRET in .env"
        );
    }

    /**
     * Issue password grant token
     * Passport handles all authentication logic
     * 
     * SECURITY: 
     * - client_id can be sent by mobile app (public, used to identify app)
     * - client_secret is NEVER sent by mobile app - server adds it automatically
     * 
     * @param string $username User email
     * @param string $password User password
     * @param string|null $clientId Optional client_id to identify which app (iOS, Android, Web, etc.)
     */
    public function issuePasswordToken(
        string $username,
        string $password,
        ?string $clientId = null
    ): array {
        return $this->dispatchTokenRequest([
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'scope' => '', // Empty scope for mobile apps
        ], $clientId);
    }

    /**
     * Refresh access token
     * Passport handles token validation
     * 
     * SECURITY: 
     * - client_id can be sent by mobile app (public, used to identify app)
     * - client_secret is NEVER sent by mobile app - server adds it automatically
     * 
     * @param string $refreshToken Refresh token
     * @param string|null $clientId Optional client_id to identify which app
     */
    public function refreshToken(string $refreshToken, ?string $clientId = null): array
    {
        return $this->dispatchTokenRequest([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'scope' => '', // Empty scope for mobile apps
        ], $clientId);
    }

    /**
     * Dispatch token request to Passport OAuth endpoint
     * Auto-detects password client from request, config, or database
     * 
     * SECURITY: 
     * - client_id can be sent by mobile app (public identifier)
     * - client_secret is ALWAYS added by server from secure config (never from client)
     * - This allows identifying which app is calling while keeping secret secure
     * 
     * @param array $payload Token request payload
     * @param string|null $clientId Optional client_id from request (to identify app)
     */
    protected function dispatchTokenRequest(array $payload, ?string $clientId = null): array
    {
        // Extract client_id from payload if not provided separately
        if (!$clientId && isset($payload['client_id'])) {
            $clientId = $payload['client_id'];
        }

        try {
            // Get client credentials from secure server config
            // client_id can come from request (public), but secret is always from server
            $client = $this->getPasswordClient($clientId);
        } catch (RuntimeException $e) {
            // If client not found, return error response
            return [
                'status' => 500,
                'data' => [
                    'message' => $e->getMessage(),
                    'error' => 'passport_client_not_configured',
                ],
            ];
        }

        // Remove client credentials from payload (security: don't trust client secret)
        unset($payload['client_id'], $payload['client_secret']);

        // Server automatically adds client credentials
        // client_id from request (if valid) or default, secret always from server
        $request = Request::create('/oauth/token', 'POST', array_merge([
            'client_id' => $client['id'],
            'client_secret' => $client['secret'], // ALWAYS from server config, never from client!
        ], $payload));

        $request->headers->set('Accept', 'application/json');

        try {
            $response = app()->handle($request);

            $responseData = json_decode($response->getContent(), true) ?: [];

            // Log error responses for debugging
            if ($response->getStatusCode() !== 200) {
                Log::warning('Passport token request failed', [
                    'status' => $response->getStatusCode(),
                    'data' => $responseData,
                    'client_id' => $client['id'],
                ]);
            }

            return [
                'status' => $response->getStatusCode(),
                'data' => $responseData,
            ];
        } catch (\Exception $e) {
            Log::error('Passport token request exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'status' => 500,
                'data' => [
                    'message' => 'Failed to issue access token: ' . $e->getMessage(),
                    'error' => 'token_request_exception',
                ],
            ];
        }
    }
    public function revokeAccessToken(?string $tokenId): void
    {
        if (!$tokenId) {
            return;
        }

        PassportToken::query()->whereKey($tokenId)->update(['revoked' => true]);
    }

    public function revokeRefreshTokensByAccessTokenId(?string $tokenId): void
    {
        if (!$tokenId) {
            return;
        }

        PassportRefreshToken::query()
            ->where('access_token_id', $tokenId)
            ->update(['revoked' => true]);
    }

    public function revokeRefreshToken(?string $refreshTokenId): void
    {
        if (!$refreshTokenId) {
            return;
        }

        PassportRefreshToken::query()->whereKey($refreshTokenId)->update(['revoked' => true]);
    }

    /**
     * Issue token for public client (no client_secret)
     * Used for PKCE flow
     * 
     * @param \Illuminate\Foundation\Auth\User $user
     * @param \Laravel\Passport\Client $client
     * @return array
     */
    public function issueTokenForPublicClient($user, Client $client): array
    {
        // Use Passport's token creation directly
        // Create access token
        $accessToken = $user->createToken('mobile-app', []);
        
        // Get the token model to create refresh token
        $tokenModel = $accessToken->token;
        
        // Create refresh token manually
        $refreshTokenModel = \Laravel\Passport\RefreshToken::create([
            'id' => \Illuminate\Support\Str::random(40),
            'access_token_id' => $tokenModel->id,
            'revoked' => false,
            'expires_at' => now()->addDays(30), // 30 days for refresh token
        ]);

        return [
            'status' => 200,
            'data' => [
                'access_token' => $accessToken->accessToken,
                'refresh_token' => $refreshTokenModel->id,
                'token_type' => 'Bearer',
                'expires_in' => $tokenModel->expires_at 
                    ? now()->diffInSeconds($tokenModel->expires_at) 
                    : config('passport.tokens_expire_in', 31536000),
            ],
        ];
    }

    /**
     * Refresh token for public client (no client_secret)
     * 
     * @param string $refreshToken
     * @param string|null $clientId
     * @param string|null $deviceId
     * @return array
     */
    public function refreshTokenForPublicClient(
        string $refreshToken,
        ?string $clientId = null,
        ?string $deviceId = null
    ): array {
        // Find the refresh token
        $refreshTokenModel = PassportRefreshToken::where('id', $refreshToken)
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$refreshTokenModel) {
            return [
                'status' => 401,
                'data' => [
                    'message' => 'Invalid or expired refresh token',
                    'error' => 'invalid_refresh_token',
                ],
            ];
        }

        // Get access token
        $accessToken = PassportToken::find($refreshTokenModel->access_token_id);
        if (!$accessToken || $accessToken->revoked) {
            return [
                'status' => 401,
                'data' => [
                    'message' => 'Invalid or revoked access token',
                    'error' => 'invalid_access_token',
                ],
            ];
        }

        // Validate client_id if provided
        if ($clientId && (string) $accessToken->client_id !== $clientId) {
            return [
                'status' => 400,
                'data' => [
                    'message' => 'Invalid client_id',
                    'error' => 'invalid_client',
                ],
            ];
        }

        // Revoke old tokens
        $accessToken->update(['revoked' => true]);
        $refreshTokenModel->update(['revoked' => true]);

        // Get user
        $user = $accessToken->user;
        if (!$user) {
            return [
                'status' => 404,
                'data' => [
                    'message' => 'User not found',
                    'error' => 'user_not_found',
                ],
            ];
        }

        // Issue new token
        $newAccessToken = $user->createToken('mobile-app', []);
        $newTokenModel = $newAccessToken->token;
        
        // Create new refresh token
        $newRefreshToken = PassportRefreshToken::create([
            'id' => \Illuminate\Support\Str::random(40),
            'access_token_id' => $newTokenModel->id,
            'revoked' => false,
            'expires_at' => now()->addDays(30),
        ]);

        return [
            'status' => 200,
            'data' => [
                'access_token' => $newAccessToken->accessToken,
                'refresh_token' => $newRefreshToken->id,
                'token_type' => 'Bearer',
                'expires_in' => $newTokenModel->expires_at 
                    ? now()->diffInSeconds($newTokenModel->expires_at) 
                    : config('passport.tokens_expire_in', 31536000),
            ],
        ];
    }
}
