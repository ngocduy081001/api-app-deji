<?php

namespace App\Services;

use App\Models\Device;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Vendor\Customer\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\Clock\SystemClock;

class JwtTokenService
{
    private Configuration $jwtConfig;
    private int $accessTokenTtl; // in seconds
    private int $refreshTokenTtl; // in seconds

    public function __construct()
    {
        $secret = config('app.key');
        if (str_starts_with($secret, 'base64:')) {
            $secret = base64_decode(substr($secret, 7));
        }

        $this->jwtConfig = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($secret)
        );

        $this->jwtConfig->setValidationConstraints(
            new SignedWith($this->jwtConfig->signer(), $this->jwtConfig->signingKey()),
            new StrictValidAt(SystemClock::fromSystemTimezone())
        );

        $this->accessTokenTtl = (int) config('auth.access_token_ttl', 900); // 15 minutes default
        $this->refreshTokenTtl = (int) config('auth.refresh_token_ttl', 1209600); // 14 days default
    }

    /**
     * Generate access token (JWT)
     */
    public function generateAccessToken(Authenticatable $user, string $deviceId): string
    {
        $now = Carbon::now();
        $expiresAt = $now->copy()->addSeconds($this->accessTokenTtl);

        $token = $this->jwtConfig->builder()
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy(Str::uuid()->toString())
            ->issuedAt($now->toImmutable())
            ->expiresAt($expiresAt->toImmutable())
            ->withClaim('user_id', $user->id)
            ->withClaim('device_id', $deviceId)
            ->withClaim('type', 'access')
            ->getToken($this->jwtConfig->signer(), $this->jwtConfig->signingKey());

        return $token->toString();
    }

    /**
     * Generate refresh token (random string, stored hashed in DB)
     */
    public function generateRefreshToken(): string
    {
        return Str::random(64);
    }

    /**
     * Hash refresh token for storage
     */
    public function hashRefreshToken(string $token): string
    {
        return Hash::make($token);
    }

    /**
     * Verify refresh token hash
     */
    public function verifyRefreshToken(string $token, string $hash): bool
    {
        return Hash::check($token, $hash);
    }

    /**
     * Validate and parse access token
     */
    public function validateAccessToken(string $token): ?array
    {
        try {
            $parsedToken = $this->jwtConfig->parser()->parse($token);
            
            $constraints = $this->jwtConfig->validationConstraints();
            if (!$this->jwtConfig->validator()->validate($parsedToken, ...$constraints)) {
                return null;
            }

            return [
                'user_id' => $parsedToken->claims()->get('user_id'),
                'device_id' => $parsedToken->claims()->get('device_id'),
                'type' => $parsedToken->claims()->get('type'),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create or update device
     */
    public function createOrUpdateDevice(
        Authenticatable $user,
        string $deviceId,
        ?string $deviceName = null,
        ?string $platform = null,
        ?string $fcmToken = null
    ): Device {
        return Device::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_id' => $deviceId,
            ],
            [
                'device_name' => $deviceName,
                'platform' => $platform,
                'fcm_token' => $fcmToken,
                'last_used_at' => Carbon::now(),
            ]
        );
    }

    /**
     * Store refresh token in database
     */
    public function storeRefreshToken(
        Authenticatable $user,
        Device $device,
        string $refreshToken,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): RefreshToken {
        // Revoke old refresh tokens for this device
        RefreshToken::where('user_id', $user->id)
            ->where('device_id', $device->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => Carbon::now()]);

        return RefreshToken::create([
            'user_id' => $user->id,
            'device_id' => $device->id,
            'token_hash' => $this->hashRefreshToken($refreshToken),
            'expires_at' => Carbon::now()->addSeconds($this->refreshTokenTtl),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Find and validate refresh token
     */
    public function findRefreshToken(string $token, string $deviceId): ?RefreshToken
    {
        $device = Device::where('device_id', $deviceId)->first();
        
        if (!$device) {
            return null;
        }

        $tokens = RefreshToken::where('device_id', $device->id)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', Carbon::now())
            ->get();

        foreach ($tokens as $refreshToken) {
            if ($this->verifyRefreshToken($token, $refreshToken->token_hash)) {
                return $refreshToken;
            }
        }

        return null;
    }

    /**
     * Revoke refresh token
     */
    public function revokeRefreshToken(string $token, string $deviceId): bool
    {
        $refreshToken = $this->findRefreshToken($token, $deviceId);
        
        if ($refreshToken && !$refreshToken->isRevoked()) {
            $refreshToken->update(['revoked_at' => Carbon::now()]);
            return true;
        }

        return false;
    }

    /**
     * Revoke all refresh tokens for a user
     */
    public function revokeAllRefreshTokens(Authenticatable $user): int
    {
        return RefreshToken::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => Carbon::now()]);
    }

    /**
     * Revoke all refresh tokens for a device
     */
    public function revokeDeviceRefreshTokens(Authenticatable $user, string $deviceId): int
    {
        $device = Device::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->first();

        if (!$device) {
            return 0;
        }

        return RefreshToken::where('device_id', $device->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => Carbon::now()]);
    }

    /**
     * Check for refresh token reuse (security: if old token is used, revoke all)
     */
    public function detectTokenReuse(string $token, string $deviceId, Authenticatable $user): bool
    {
        $device = Device::where('device_id', $deviceId)->first();
        
        if (!$device) {
            return false;
        }

        // If token is found but already revoked, it's a reuse attempt
        $revokedTokens = RefreshToken::where('device_id', $device->id)
            ->whereNotNull('revoked_at')
            ->get();

        foreach ($revokedTokens as $revokedToken) {
            if ($this->verifyRefreshToken($token, $revokedToken->token_hash)) {
                // Token reuse detected - revoke all tokens for security
                $this->revokeAllRefreshTokens($user);
                return true;
            }
        }

        return false;
    }

    /**
     * Get access token TTL
     */
    public function getAccessTokenTtl(): int
    {
        return $this->accessTokenTtl;
    }

    /**
     * Get refresh token TTL
     */
    public function getRefreshTokenTtl(): int
    {
        return $this->refreshTokenTtl;
    }
}

