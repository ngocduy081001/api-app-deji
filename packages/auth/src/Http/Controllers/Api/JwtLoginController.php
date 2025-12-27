<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JwtTokenService;
use Vendor\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class JwtLoginController extends Controller
{
    public function __construct(
        protected JwtTokenService $tokenService
    ) {}

    /**
     * Login customer
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_id' => 'required|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'platform' => 'nullable|in:ios,android',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find customer
        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !$customer->password || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Create or update device
        $device = $this->tokenService->createOrUpdateDevice(
            $customer,
            $request->device_id,
            $request->device_name,
            $request->platform,
            $request->fcm_token
        );

        // Generate tokens
        $accessToken = $this->tokenService->generateAccessToken($customer, $request->device_id);
        $refreshToken = $this->tokenService->generateRefreshToken();

        // Store refresh token
        $this->tokenService->storeRefreshToken(
            $customer,
            $device,
            $refreshToken,
            $request->ip(),
            $request->userAgent()
        );

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => $this->tokenService->getAccessTokenTtl(),
            ],
        ], 200);
    }
}
