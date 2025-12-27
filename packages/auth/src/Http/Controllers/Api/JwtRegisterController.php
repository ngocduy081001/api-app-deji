<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JwtTokenService;
use App\Services\OtpService;
use Vendor\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class JwtRegisterController extends Controller
{
    public function __construct(
        protected JwtTokenService $tokenService,
        protected OtpService $otpService
    ) {}

    /**
     * Register a new customer (requires OTP verification)
     * POST /api/auth/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
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
            'phone' => 'nullable|string|max:20',
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

        // Check if OTP was already verified (marked as used)
        // Frontend already verified OTP via /auth/verify-otp endpoint
        $otp = \App\Models\Otp::forEmailAndPurpose($request->email, 'register')
            ->where('otp_code', $request->otp_code)
            ->where('is_used', true)
            ->where('used_at', '>=', now()->subMinutes(5)) // OTP must be verified within last 5 minutes
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Mã OTP chưa được xác thực hoặc đã hết hạn. Vui lòng xác thực OTP trước.',
            ], 400);
        }

        // Check if customer already exists with this email
        $customer = Customer::where('email', $request->email)->first();
        $isNewCustomer = false;

        if ($customer) {
            // Customer exists - check if already has password (already registered)
            if ($customer->password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already registered',
                    'errors' => ['email' => ['This email is already registered. Please use login instead.']]
                ], 422);
            }

            // Customer exists but no password - update with password and other info
            $customer->update([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'phone' => $request->phone ?? $customer->phone,
                'email_verified_at' => now(), // Mark email as verified after OTP
            ]);
        } else {
            // Create new customer
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone ?? null,
                'email_verified_at' => now(), // Mark email as verified after OTP
            ]);
            $isNewCustomer = true;
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
            'message' => $isNewCustomer ? 'User registered successfully' : 'Password set successfully. You can now login.',
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
        ], 201);
    }
}
