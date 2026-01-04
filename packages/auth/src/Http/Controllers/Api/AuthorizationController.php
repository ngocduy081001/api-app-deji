<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Otp;
use Vendor\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Laravel\Passport\AuthCode;
use Carbon\Carbon;

class AuthorizationController extends Controller
{
    /**
     * Issue authorization code with PKCE
     * 
     * OAuth 2.0 Authorization Code + PKCE flow
     * - Validates credentials
     * - Stores code_challenge for PKCE validation
     * - Returns authorization_code for token exchange
     * 
     * SECURITY:
     * - No client_secret required (public client)
     * - PKCE code_challenge stored for validation
     * - redirect_uri whitelist validation
     */
    public function authorize(Request $request)
    {
        $validated = $request->validate([
            'grant_type' => 'required|in:authorization_code',
            'client_id' => 'required|string',
            'redirect_uri' => ['nullable', 'string', function ($attribute, $value, $fail) {
                // Optional for mobile apps - validate only if provided
                if ($value && !preg_match('/^[a-z][a-z0-9+.-]*:\/\/[^\s]*$/', $value)) {
                    $fail('Định dạng redirect uri không hợp lệ. Phải có dạng: scheme://path (ví dụ: com.app://oauth/callback)');
                }
            }],
            'code_challenge' => 'required|string|min:43|max:128',
            'code_challenge_method' => 'required|in:S256',
            'email' => 'required|email',
            'password' => 'required|string',
            'device_id' => 'nullable|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'platform' => 'nullable|in:ios,android',
            'fcm_token' => 'nullable|string',
            // Registration fields
            'name' => 'nullable|string|max:255',
            'password_confirmation' => 'nullable|string',
            'otp_code' => 'nullable|string|size:6',
        ]);

        // Validate client_id (public client, no secret check)
        $client = Client::where('id', $validated['client_id'])
            ->where('revoked', false)
            ->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid client_id',
            ], 400);
        }

        // Validate redirect_uri (whitelist check)

        if ($request->filled('redirect_uri')) {
            if (!$this->isValidRedirectUri($request->input('redirect_uri'), $client)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid redirect_uri',
                ], 400);
            }
        }

        // Authenticate user
        $customer = Customer::where('email', $validated['email'])->first();

        if (!$customer || !Hash::check($validated['password'], $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Handle registration flow (if name provided)
        if ($request->filled('name')) {
            // Validate registration data
            $regValidator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&]/',
                    'confirmed',
                ],
                'otp_code' => 'nullable|string|size:6',
            ]);

            if ($regValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $regValidator->errors(),
                ], 422);
            }

            // Verify OTP if provided
            if ($request->filled('otp_code')) {
                $otp = Otp::forEmailAndPurpose($validated['email'], 'register')
                    ->where('otp_code', $validated['otp_code'])
                    ->where('is_used', true)
                    ->where('used_at', '>=', now()->subMinutes(5))
                    ->first();

                if (!$otp) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mã OTP chưa được xác thực hoặc đã hết hạn',
                    ], 400);
                }
            }

            // Update customer if needed
            if (!$customer->name || !$customer->password) {
                $customer->update([
                    'name' => $validated['name'],
                    'password' => Hash::make($validated['password']),
                    'email_verified_at' => $request->filled('otp_code') ? now() : null,
                ]);
            }
        }

        // Handle device management
        if ($request->filled('device_id')) {
            Device::updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'device_id' => $validated['device_id'],
                ],
                [
                    'device_name' => $validated['device_name'] ?? null,
                    'platform' => $validated['platform'] ?? null,
                    'fcm_token' => $validated['fcm_token'] ?? null,
                    'last_used_at' => now(),
                ]
            );
        }

        // Generate authorization code
        // Use default redirect_uri for mobile apps if not provided
        $redirectUri = $validated['redirect_uri'] ?? 'mobile-app://oauth/callback';
        $authCode = $this->createAuthorizationCode(
            $customer,
            $client,
            $validated['code_challenge'],
            $validated['code_challenge_method'],
            $redirectUri
        );

        return response()->json([
            'success' => true,
            'message' => 'Authorization code issued',
            'data' => [
                'authorization_code' => $authCode,
                'expires_in' => 600, // 10 minutes
            ],
        ], 200);
    }

    /**
     * Create authorization code with PKCE data
     */
    protected function createAuthorizationCode(
        Customer $customer,
        Client $client,
        string $codeChallenge,
        string $codeChallengeMethod,
        string $redirectUri
    ): string {
        $code = Str::random(80);

        DB::table('oauth_auth_codes')->insert([
            'id' => $code,
            'user_id' => $customer->id,
            'client_id' => $client->id,
            'scopes' => json_encode([]),
            'revoked' => false,
            'expires_at' => Carbon::now()->addMinutes(10),
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => $codeChallengeMethod,
            'redirect_uri' => $redirectUri,
        ]);

        return $code;
    }

    /**
     * Validate redirect_uri against whitelist
     */
    protected function isValidRedirectUri(string $redirectUri, Client $client): bool
    {
        // Get allowed redirect URIs from config or client metadata
        $allowedUris = config('services.passport.allowed_redirect_uris', []);

        // Add default mobile app redirect URIs (support multiple formats)
        $defaultUris = [
            'com.app://oauth/callback',
            'com.app://oauth',
            'com.app://',
            'exp://oauth/callback',
            'exp://oauth',
            'exp://',
        ];

        $allAllowed = array_merge($allowedUris, $defaultUris);

        // Check if redirect_uri matches any allowed URI (exact match)
        foreach ($allAllowed as $allowed) {
            if ($redirectUri === $allowed) {
                return true;
            }
        }

        // Also check if redirect_uri starts with any allowed scheme
        // This allows flexibility for paths like com.app://oauth/callback/success
        foreach ($allAllowed as $allowed) {
            if (strpos($redirectUri, $allowed) === 0) {
                return true;
            }
        }

        return false;
    }
}
