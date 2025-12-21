<?php

namespace Vendor\Auth\Services;

use Illuminate\Http\Request;
use Laravel\Passport\RefreshToken as PassportRefreshToken;
use Laravel\Passport\Token as PassportToken;
use RuntimeException;

class PassportTokenService
{
    public function issuePasswordToken(
        string $username,
        string $password,
        string $scope = '',
        ?string $clientId = null,
        ?string $clientSecret = null,
    ): array
    {
        return $this->dispatchTokenRequest([
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'scope' => $scope,
        ], $clientId, $clientSecret);
    }

    public function refreshToken(string $refreshToken, string $scope = '', ?string $clientId = null, ?string $clientSecret = null): array
    {
        return $this->dispatchTokenRequest([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'scope' => $scope,
        ], $clientId, $clientSecret);
    }

    protected function dispatchTokenRequest(array $payload, ?string $clientId = null, ?string $clientSecret = null): array
    {
        $clientId = $clientId ?? config('services.passport.password_client_id');
        $clientSecret = $clientSecret ?? config('services.passport.password_client_secret');

        if (!$clientId || !$clientSecret) {
            throw new RuntimeException('Passport password client is not configured.');
        }

        $request = Request::create('/oauth/token', 'POST', array_merge([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ], $payload));

        $request->headers->set('Accept', 'application/json');

        $response = app()->handle($request);

        return [
            'status' => $response->getStatusCode(),
            'data' => json_decode($response->getContent(), true) ?: [],
        ];
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
}

