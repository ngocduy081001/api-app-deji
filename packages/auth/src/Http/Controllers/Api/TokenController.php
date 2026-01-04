<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Vendor\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Client;
use Vendor\Auth\Services\PassportTokenService;

class TokenController extends Controller
{
    public function __construct(
        protected PassportTokenService $passportTokenService,
    ) {}

    /**
     * Exchange authorization code for tokens
     * Validates PKCE code_verifier
     * 
     * OAuth 2.0 Authorization Code + PKCE flow
     * - Validates authorization code
     * - Validates PKCE code_verifier against stored code_challenge
     * - Issues access_token and refresh_token
     * 
     * SECURITY:
     * - No client_secret required (public client)
     * - PKCE validation prevents code interception attacks
     */
    public function token(Request $request)
    {
        $validated = $request->validate([
            'grant_type' => 'required|in:authorization_code',
            'client_id' => 'required|string',
            'redirect_uri' => ['nullable', 'string', function ($attribute, $value, $fail) {
                // Optional for mobile apps - validate only if provided
                if ($value && !preg_match('/^[a-z][a-z0-9+.-]*:\/\//', $value)) {
                    $fail('The redirect_uri must be a valid URL scheme.');
                }
            }],
            'code' => 'required|string',
            'code_verifier' => 'required|string|min:43|max:128',
        ]);

        // Validate client_id
        $client = Client::where('id', $validated['client_id'])
            ->where('revoked', false)
            ->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid client_id',
            ], 400);
        }

        // Get authorization code
        $authCode = DB::table('oauth_auth_codes')
            ->where('id', $validated['code'])
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->where('client_id', $client->id)
            ->first();

        if (!$authCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired authorization code',
            ], 400);
        }

        $requestRedirectUri = $validated['redirect_uri'] ?? 'mobile-app://oauth/callback';

        if ($authCode->redirect_uri !== $requestRedirectUri) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid redirect_uri',
            ], 400);
        }

        // Validate PKCE code_verifier
        if (!$this->validatePKCE(
            $validated['code_verifier'],
            $authCode->code_challenge,
            $authCode->code_challenge_method
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid code_verifier',
            ], 400);
        }

        // Get user
        $customer = Customer::find($authCode->user_id);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Revoke authorization code (one-time use)
        DB::table('oauth_auth_codes')
            ->where('id', $validated['code'])
            ->update(['revoked' => true]);

        // Issue tokens using Passport
        // For public clients, we'll use a modified token issuance
        $tokenResponse = $this->passportTokenService->issueTokenForPublicClient(
            $customer,
            $client
        );

        if ($tokenResponse['status'] !== 200) {
            return response()->json([
                'success' => false,
                'message' => $tokenResponse['data']['message'] ?? 'Failed to issue tokens',
                'errors' => $tokenResponse['data']['errors'] ?? null,
            ], $tokenResponse['status']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tokens issued successfully',
            'data' => [
                'user' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
                'access_token' => $tokenResponse['data']['access_token'],
                'refresh_token' => $tokenResponse['data']['refresh_token'] ?? null,
                'token_type' => $tokenResponse['data']['token_type'] ?? 'Bearer',
                'expires_in' => $tokenResponse['data']['expires_in'] ?? null,
            ],
        ], 200);
    }

    /**
     * Validate PKCE code_verifier against code_challenge
     * RFC 7636: code_challenge = BASE64URL(SHA256(ASCII(code_verifier)))
     */
    protected function validatePKCE(
        string $codeVerifier,
        ?string $codeChallenge,
        ?string $codeChallengeMethod
    ): bool {
        if (!$codeChallenge || !$codeChallengeMethod) {
            return false;
        }

        if ($codeChallengeMethod !== 'S256') {
            return false;
        }

        // Calculate code_challenge from code_verifier
        // SHA256 hash of code_verifier
        $hash = hash('sha256', $codeVerifier, true);

        // Convert to base64url (URL-safe base64)
        $calculatedChallenge = rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');

        // Compare with stored challenge (constant-time comparison)
        return hash_equals($codeChallenge, $calculatedChallenge);
    }
}
