<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Vendor\Auth\Services\PassportTokenService;

class LoginController extends Controller
{
    public function __construct(
        protected PassportTokenService $passportTokenService,

    ) {
    }

    /**
     * Login user and return access token and refresh token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'scope' => 'nullable|string',
            'client_id' => 'nullable|string|required_with:client_secret',
            'client_secret' => 'nullable|string|required_with:client_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $tokenResponse = $this->passportTokenService->issuePasswordToken(
            $request->email,
            $request->password,
            $this->resolveScope($request),
            $request->input('client_id'),
            $request->input('client_secret'),
        );

        if ($tokenResponse['status'] !== 200) {
            return $this->tokenErrorResponse($tokenResponse);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'access_token' => $tokenResponse['data']['access_token'],
                'refresh_token' => $tokenResponse['data']['refresh_token'] ?? null,
                'token_type' => $tokenResponse['data']['token_type'] ?? 'Bearer',
                'expires_in' => $tokenResponse['data']['expires_in'] ?? null,
                'expires_at' => now()->addSeconds($tokenResponse['data']['expires_in'] ?? 0)->toIso8601String(),
            ]
        ], 200);
    }

    /**
     * Refresh access token using refresh token.
     */
    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
            'scope' => 'nullable|string',
            'client_id' => 'nullable|string|required_with:client_secret',
            'client_secret' => 'nullable|string|required_with:client_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $tokenResponse = $this->passportTokenService->refreshToken(
            $request->refresh_token,
            $this->resolveScope($request),
            $request->input('client_id'),
            $request->input('client_secret'),
        );

        if ($tokenResponse['status'] !== 200) {
            return $this->tokenErrorResponse($tokenResponse);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'access_token' => $tokenResponse['data']['access_token'],
                'refresh_token' => $tokenResponse['data']['refresh_token'] ?? $request->refresh_token,
                'token_type' => $tokenResponse['data']['token_type'] ?? 'Bearer',
                'expires_in' => $tokenResponse['data']['expires_in'] ?? null,
                'expires_at' => now()->addSeconds($tokenResponse['data']['expires_in'] ?? 0)->toIso8601String(),
            ]
        ], 200);
    }

    /**
     * Logout user by revoking refresh token.
     */
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        if ($token = $user->token()) {
            $this->passportTokenService->revokeAccessToken($token->id);
            $this->passportTokenService->revokeRefreshTokensByAccessTokenId($token->id);
        }

        if ($request->filled('refresh_token')) {
            $this->passportTokenService->revokeRefreshToken($request->input('refresh_token'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ], 200);
    }

    /**
     * Logout from all devices by revoking all refresh tokens.
     */
    public function logoutAll(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $count = 0;

        foreach ($user->tokens as $token) {
            $this->passportTokenService->revokeAccessToken($token->id);
            $this->passportTokenService->revokeRefreshTokensByAccessTokenId($token->id);
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "Logged out from all devices successfully. {$count} token(s) revoked."
        ], 200);
    }

    /**
     * Get current authenticated user info.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
            ]
        ], 200);
    }

    protected function resolveScope(Request $request): string
    {
        return $request->input('scope', config('services.passport.default_scope', ''));
    }

    protected function tokenErrorResponse(array $tokenResponse)
    {
        $status = $tokenResponse['status'] ?? 500;
        $data = $tokenResponse['data'] ?? [];

        return response()->json([
            'success' => false,
            'message' => $data['message'] ?? 'Unable to issue access token',
            'errors' => $data['errors'] ?? ($data ? [$data] : null),
        ], $status);
    }
}
