<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JwtTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Vendor\Customer\Models\Customer;

class JwtAuthController extends Controller
{
    public function __construct(
        protected JwtTokenService $tokenService
    ) {}

    /**
     * Refresh access token
     * POST /api/auth/refresh
     */
    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
            'device_id' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find refresh token
        $refreshTokenModel = $this->tokenService->findRefreshToken(
            $request->refresh_token,
            $request->device_id
        );

        if (!$refreshTokenModel) {
            return response()->json([
                'message' => 'Invalid or expired refresh token',
            ], 401);
        }

        $customer = $refreshTokenModel->user;
        $device = $refreshTokenModel->device;

        // Check for token reuse (security)
        if ($this->tokenService->detectTokenReuse(
            $request->refresh_token,
            $request->device_id,
            $customer
        )) {
            return response()->json([
                'message' => 'Token reuse detected. All tokens have been revoked for security.',
            ], 401);
        }

        // Revoke old refresh token
        $refreshTokenModel->update(['revoked_at' => now()]);

        // Generate new tokens
        $newAccessToken = $this->tokenService->generateAccessToken($customer, $device->device_id);
        $newRefreshToken = $this->tokenService->generateRefreshToken();

        // Store new refresh token
        $this->tokenService->storeRefreshToken(
            $customer,
            $device,
            $newRefreshToken,
            $request->ip(),
            $request->userAgent()
        );

        return response()->json([
            'message' => 'Token refreshed successfully',
            'data' => [
                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
                'token_type' => 'Bearer',
                'expires_in' => $this->tokenService->getAccessTokenTtl(),
            ],
        ], 200);
    }

    /**
     * Logout (single device)
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'nullable|string',
            'device_id' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer = $request->user();

        if ($request->refresh_token) {
            $this->tokenService->revokeRefreshToken(
                $request->refresh_token,
                $request->device_id
            );
        } else {
            // Revoke all tokens for this device
            $this->tokenService->revokeDeviceRefreshTokens($customer, $request->device_id);
        }

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }

    /**
     * Logout from all devices
     * POST /api/auth/logout-all
     */
    public function logoutAll(Request $request)
    {
        $customer = $request->user();
        $count = $this->tokenService->revokeAllRefreshTokens($customer);

        return response()->json([
            'message' => "Logged out from all devices. {$count} token(s) revoked.",
        ], 200);
    }

    /**
     * Get current authenticated user
     * GET /api/auth/me
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
            ],
        ], 200);
    }
}

