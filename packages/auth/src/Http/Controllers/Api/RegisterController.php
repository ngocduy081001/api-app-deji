<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Otp;
use Vendor\Customer\Models\Customer;
use Vendor\Customer\Models\CustomerSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Vendor\Auth\Services\PassportTokenService;

class RegisterController extends Controller
{
    public function __construct(
        protected PassportTokenService $passportTokenService
    ) {}

    /**
     * Register a new user.
     * Supports OTP verification and device management for mobile apps.
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
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed',
            ],
            'otp_code' => 'nullable|string|size:6',
            'device_id' => 'nullable|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'platform' => 'nullable|in:ios,android',
            'fcm_token' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ], [
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.regex' => 'Mật khẩu phải chứa chữ hoa, chữ thường, số và ký tự đặc biệt',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // If OTP code is provided, verify it
        if ($request->filled('otp_code')) {
            $otp = Otp::forEmailAndPurpose($request->email, 'register')
                ->where('otp_code', $request->otp_code)
                ->where('is_used', true)
                ->where('used_at', '>=', now()->subMinutes(5))
                ->first();

            if (!$otp) {
                return response()->json([
                    'message' => 'Mã OTP chưa được xác thực hoặc đã hết hạn. Vui lòng xác thực OTP trước.',
                ], 400);
            }
        }

        // Check if customer already exists with this email
        $customer = Customer::where('email', $request->email)->first();
        $isNewCustomer = false;

        if ($customer) {
            // Customer exists - check if already has password (already registered)
            if ($customer->password) {
                return response()->json([
                    'message' => 'Email already registered',
                    'errors' => ['email' => ['This email is already registered. Please use login instead.']]
                ], 422);
            }

            // Customer exists but no password - update with password and other info
            $customer->update([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'phone' => $request->phone ?? $customer->phone,
                'email_verified_at' => $request->filled('otp_code') ? now() : null,
            ]);
        } else {
            // Create new customer
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone ?? null,
                'email_verified_at' => $request->filled('otp_code') ? now() : null,
            ]);
            $isNewCustomer = true;
        }

        // Create default settings for new customer
        if ($isNewCustomer) {
            CustomerSetting::getOrCreate($customer->id);
        }

        // Handle device management if device_id is provided
        $device = null;
        if ($request->filled('device_id')) {
            $device = $this->createOrUpdateDevice(
                $customer,
                $request->device_id,
                $request->device_name ?? null,
                $request->platform ?? null,
                $request->fcm_token ?? null
            );
        }

        // Passport handles token generation
        $tokenResponse = $this->passportTokenService->issuePasswordToken(
            $request->email,
            $request->password
        );

        if ($tokenResponse['status'] !== 200) {
            return response()->json([
                'message' => $tokenResponse['data']['message'] ?? 'Unable to issue access token',
                'errors' => $tokenResponse['data']['errors'] ?? null,
            ], $tokenResponse['status']);
        }

        return response()->json([
            'message' => $isNewCustomer ? 'User registered successfully' : 'Password set successfully. You can now login.',
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
                'expires_at' => now()->addSeconds($tokenResponse['data']['expires_in'] ?? 0)->toIso8601String(),
            ]
        ], 201);
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
