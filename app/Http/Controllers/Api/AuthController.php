<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use App\Services\JwtTokenService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected JwtTokenService $tokenService;
    protected OtpService $otpService;

    public function __construct(JwtTokenService $tokenService, OtpService $otpService)
    {
        $this->tokenService = $tokenService;
        $this->otpService = $otpService;
    }

    /**
     * Check if email exists
     * POST /api/auth/check-email
     */
    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->checkEmailExists($request->email);

        return response()->json($result, 200);
    }

    /**
     * Send OTP to email
     * POST /api/auth/send-otp
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'purpose' => 'nullable|string|in:register,reset_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->sendOtp(
            $request->email,
            $request->purpose ?? 'register',
            $request->ip()
        );

        $statusCode = $result['success'] ? 200 : 400;
        return response()->json($result, $statusCode);
    }

    /**
     * Verify OTP
     * POST /api/auth/verify-otp
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
            'purpose' => 'nullable|string|in:register,reset_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->verifyOtp(
            $request->email,
            $request->otp_code,
            $request->purpose ?? 'register'
        );

        $statusCode = $result['success'] ? 200 : 400;
        return response()->json($result, $statusCode);
    }

    /**
     * Register a new user (requires OTP verification)
     * POST /api/auth/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',      // at least one lowercase letter
                'regex:/[A-Z]/',      // at least one uppercase letter
                'regex:/[0-9]/',      // at least one digit
                'regex:/[@$!%*#?&]/', // at least one special character
                'confirmed',
            ],
            'otp_code' => 'required|string|size:6',
            'device_id' => 'required|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'platform' => 'nullable|in:ios,android',
            'fcm_token' => 'nullable|string',
        ], [
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.regex' => 'Mật khẩu phải chứa chữ hoa, chữ thường, số và ký tự đặc biệt',
            'otp_code.required' => 'Mã OTP là bắt buộc',
            'otp_code.size' => 'Mã OTP phải có 6 chữ số',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify OTP first
        $otpResult = $this->otpService->verifyOtp($request->email, $request->otp_code, 'register');
        if (!$otpResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $otpResult['message'],
            ], 400);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Mark email as verified after OTP
        ]);

        // Create or update device
        $device = $this->tokenService->createOrUpdateDevice(
            $user,
            $request->device_id,
            $request->device_name,
            $request->platform,
            $request->fcm_token
        );

        // Generate tokens
        $accessToken = $this->tokenService->generateAccessToken($user, $request->device_id);
        $refreshToken = $this->tokenService->generateRefreshToken();

        // Store refresh token
        $this->tokenService->storeRefreshToken(
            $user,
            $device,
            $refreshToken,
            $request->ip(),
            $request->userAgent()
        );

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => $this->tokenService->getAccessTokenTtl(),
            ],
        ], 201);
    }

    /**
     * Login user
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

        // Find user
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Create or update device
        $device = $this->tokenService->createOrUpdateDevice(
            $user,
            $request->device_id,
            $request->device_name,
            $request->platform,
            $request->fcm_token
        );

        // Generate tokens
        $accessToken = $this->tokenService->generateAccessToken($user, $request->device_id);
        $refreshToken = $this->tokenService->generateRefreshToken();

        // Store refresh token
        $this->tokenService->storeRefreshToken(
            $user,
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
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => $this->tokenService->getAccessTokenTtl(),
            ],
        ], 200);
    }

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
                'success' => false,
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
                'success' => false,
                'message' => 'Invalid or expired refresh token',
            ], 401);
        }

        $user = $refreshTokenModel->user;
        $device = $refreshTokenModel->device;

        // Check for token reuse (security)
        if ($this->tokenService->detectTokenReuse(
            $request->refresh_token,
            $request->device_id,
            $user
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Token reuse detected. All tokens have been revoked for security.',
            ], 401);
        }

        // Revoke old refresh token
        $refreshTokenModel->update(['revoked_at' => now()]);

        // Generate new tokens
        $newAccessToken = $this->tokenService->generateAccessToken($user, $device->device_id);
        $newRefreshToken = $this->tokenService->generateRefreshToken();

        // Store new refresh token
        $this->tokenService->storeRefreshToken(
            $user,
            $device,
            $newRefreshToken,
            $request->ip(),
            $request->userAgent()
        );

        return response()->json([
            'success' => true,
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
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if ($request->refresh_token) {
            $this->tokenService->revokeRefreshToken(
                $request->refresh_token,
                $request->device_id
            );
        } else {
            // Revoke all tokens for this device
            $this->tokenService->revokeDeviceRefreshTokens($user, $request->device_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ], 200);
    }

    /**
     * Logout from all devices
     * POST /api/auth/logout-all
     */
    public function logoutAll(Request $request)
    {
        $user = $request->user();
        $count = $this->tokenService->revokeAllRefreshTokens($user);

        return response()->json([
            'success' => true,
            'message' => "Logged out from all devices. {$count} token(s) revoked.",
        ], 200);
    }

    /**
     * Get current authenticated user
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
            ],
        ], 200);
    }
}
