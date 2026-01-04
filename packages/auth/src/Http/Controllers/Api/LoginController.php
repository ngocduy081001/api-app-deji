<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Vendor\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Vendor\Auth\Services\PassportTokenService;

class LoginController extends Controller
{
    public function __construct(
        protected PassportTokenService $passportTokenService,
    ) {}

    /**
     * Login user and return access token and refresh token.
     * Passport handles all authentication logic.
     * Supports device management for mobile apps.
     * 
     * SECURITY NOTE: 
     * - client_id can be sent by mobile app (public identifier, used to identify which app)
     * - client_secret is NEVER sent by mobile app - server adds it automatically from secure config
     * - This allows identifying which app is calling (iOS, Android, Web, etc.) while keeping secret secure
     * - If client_id not provided, server uses default configured client
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'client_id' => 'nullable|string', // Optional: to identify which app (iOS, Android, Web, etc.)
            'device_id' => 'nullable|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'platform' => 'nullable|in:ios,android',
            'fcm_token' => 'nullable|string',
            // NOTE: client_secret is NOT accepted - server adds it automatically from secure config
        ]);

        // Passport handles credential verification and token generation
        // client_id from request (if provided) identifies which app
        // client_secret is always added by server from secure config
        $tokenResponse = $this->passportTokenService->issuePasswordToken(
            $validated['email'],
            $validated['password'],
            $validated['client_id'] ?? null
        );

        if ($tokenResponse['status'] !== 200) {
            return $this->tokenErrorResponse($tokenResponse);
        }

        // Get authenticated user (Passport already verified user exists)
        $customer = Customer::where('email', $validated['email'])->first();

        // Handle device management if device_id is provided
        $device = null;
        if ($request->filled('device_id')) {
            $device = $this->createOrUpdateDevice(
                $customer,
                $validated['device_id'],
                $validated['device_name'] ?? null,
                $validated['platform'] ?? null,
                $validated['fcm_token'] ?? null
            );
        }

        return response()->json([
            'message' => 'Login successful',
            'data' => $this->formatTokenResponse($tokenResponse['data'], $customer),
        ], 200);
    }

    /**
     * Refresh access token using refresh token.
     * Supports both password grant (with client_secret) and public client (PKCE, no secret).
     * 
     * SECURITY: 
     * - For public clients: client_id only, no client_secret
     * - For password grant: client_secret added by server
     */
    public function refresh(Request $request)
    {
        $validated = $request->validate([
            'grant_type' => 'nullable|in:refresh_token',
            'refresh_token' => 'required|string',
            'client_id' => 'nullable|string', // Required for public clients
            'device_id' => 'nullable|string', // For device binding
        ]);

        // Check if this is a public client request (no client_secret in request)
        // Public clients use PKCE flow and don't send client_secret
        $isPublicClient = !$request->has('client_secret');

        if ($isPublicClient) {
            // Public client refresh (PKCE flow)
            $tokenResponse = $this->passportTokenService->refreshTokenForPublicClient(
                $validated['refresh_token'],
                $validated['client_id'] ?? null,
                $validated['device_id'] ?? null
            );
        } else {
            // Password grant refresh (legacy, with client_secret)
            $tokenResponse = $this->passportTokenService->refreshToken(
                $validated['refresh_token'],
                $validated['client_id'] ?? null
            );
        }

        if ($tokenResponse['status'] !== 200) {
            return $this->tokenErrorResponse($tokenResponse);
        }

        // Get user from token
        $customer = null;
        if (isset($tokenResponse['data']['access_token'])) {
            // Extract user_id from token or get from request
            // For now, we'll get user from the token
            try {
                $token = \Laravel\Passport\Token::where('id', $validated['refresh_token'])
                    ->where('revoked', false)
                    ->first();
                if ($token && $token->user_id) {
                    $customer = \Vendor\Customer\Models\Customer::find($token->user_id);
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => $this->formatTokenResponse($tokenResponse['data'], $customer),
        ], 200);
    }

    /**
     * Logout user by revoking tokens.
     * Passport middleware ensures user is authenticated.
     */
    public function logout(Request $request)
    {
        $customer = $request->user();

        // Revoke current access token
        if ($token = $customer->token()) {
            $this->passportTokenService->revokeAccessToken($token->id);
            $this->passportTokenService->revokeRefreshTokensByAccessTokenId($token->id);
        }

        // Revoke specific refresh token if provided
        if ($request->filled('refresh_token')) {
            $this->passportTokenService->revokeRefreshToken($request->input('refresh_token'));
        }

        return response()->json([
            'message' => 'Logout successful'
        ], 200);
    }

    /**
     * Logout from all devices by revoking all tokens.
     * Passport middleware ensures user is authenticated.
     */
    public function logoutAll(Request $request)
    {
        $customer = $request->user();
        $count = 0;

        foreach ($customer->tokens as $token) {
            $this->passportTokenService->revokeAccessToken($token->id);
            $this->passportTokenService->revokeRefreshTokensByAccessTokenId($token->id);
            $count++;
        }

        return response()->json([
            'message' => "Logged out from all devices successfully. {$count} token(s) revoked."
        ], 200);
    }

    /**
     * Get current authenticated user info.
     * Passport middleware ensures user is authenticated.
     */
    public function me(Request $request)
    {
        $customer = $request->user();

        return response()->json([
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'email_verified_at' => $customer->email_verified_at,
                'created_at' => $customer->created_at,
            ]
        ], 200);
    }

    /**
     * Format token response with user data
     */
    protected function formatTokenResponse(array $tokenData, ?Customer $customer = null): array
    {
        $response = [
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'] ?? null,
            'token_type' => $tokenData['token_type'] ?? 'Bearer',
            'expires_in' => $tokenData['expires_in'] ?? null,
            'expires_at' => isset($tokenData['expires_in'])
                ? now()->addSeconds($tokenData['expires_in'])->toIso8601String()
                : null,
        ];

        if ($customer) {
            $response['user'] = [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
            ];
        }

        return $response;
    }

    /**
     * Format error response from Passport
     */
    protected function tokenErrorResponse(array $tokenResponse)
    {
        $status = $tokenResponse['status'] ?? 500;
        $data = $tokenResponse['data'] ?? [];

        $errorMessage = $data['message'] ?? 'Unable to issue access token';

        // Include additional error details if available
        if (isset($data['hint'])) {
            $errorMessage .= ': ' . $data['hint'];
        } elseif (isset($data['error_description'])) {
            $errorMessage .= ': ' . $data['error_description'];
        } elseif (isset($data['error'])) {
            $errorMessage .= ': ' . $data['error'];
        }

        Log::warning('Passport token error', [
            'status' => $status,
            'data' => $data,
        ]);

        return response()->json([
            'message' => $errorMessage,
            'errors' => $data['errors'] ?? null,
        ], $status);
    }

    /**
     * Create or update device for mobile app
     */
    protected function createOrUpdateDevice(
        Customer $customer,
        string $deviceId,
        ?string $deviceName = null,
        ?string $platform = null,
        ?string $fcmToken = null
    ): Device {
        return Device::updateOrCreate(
            [
                'user_id' => $customer->id,
                'device_id' => $deviceId,
            ],
            [
                'device_name' => $deviceName,
                'platform' => $platform,
                'fcm_token' => $fcmToken,
                'last_used_at' => now(),
            ]
        );
    }
}
