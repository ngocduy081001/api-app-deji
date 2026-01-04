<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Vendor\Customer\Models\Customer;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otpService
    ) {}

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

        $exists = Customer::where('email', $request->email)->whereNotNull('password')->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Email này đã được sử dụng' : 'Email có thể sử dụng',
        ], 200);
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

        // Convert success to status code
        $statusCode = isset($result['success']) && $result['success'] ? 200 : 400;

        // Keep success field in response for consistency with other auth endpoints
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

        // Convert success to status code
        $statusCode = isset($result['success']) && $result['success'] ? 200 : 400;

        // Keep success field in response for consistency with other auth endpoints
        return response()->json($result, $statusCode);
    }
}
