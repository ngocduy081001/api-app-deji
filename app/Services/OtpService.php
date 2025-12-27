<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Vendor\Customer\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpService
{
    /**
     * OTP expiration time in minutes
     */
    const OTP_EXPIRY_MINUTES = 10;

    /**
     * OTP length
     */
    const OTP_LENGTH = 6;

    /**
     * Maximum OTP attempts per email per hour
     */
    const MAX_ATTEMPTS_PER_HOUR = 5;

    /**
     * Generate and send OTP to email
     *
     * @param string $email
     * @param string $purpose
     * @param string|null $ipAddress
     * @return array
     * @throws \Exception
     */
    public function sendOtp(string $email, string $purpose = 'register', ?string $ipAddress = null): array
    {
        // Check if email already exists (for register purpose)
        if ($purpose === 'register') {
            if (Customer::where('email', $email)->whereNotNull('password')->exists()) {
                return [
                    'success' => false,
                    'message' => 'Email này đã được sử dụng',
                ];
            }
        }

        // Check rate limiting
        $recentOtps = Otp::where('email', $email)
            ->where('purpose', $purpose)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();

        if ($recentOtps >= self::MAX_ATTEMPTS_PER_HOUR) {
            return [
                'success' => false,
                'message' => 'Bạn đã yêu cầu quá nhiều mã OTP. Vui lòng thử lại sau 1 giờ.',
            ];
        }

        // Invalidate previous unused OTPs for this email and purpose
        Otp::forEmailAndPurpose($email, $purpose)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Generate OTP code
        $otpCode = $this->generateOtpCode();

        // Create OTP record
        $otp = Otp::create([
            'email' => $email,
            'otp_code' => $otpCode,
            'purpose' => $purpose,
            'expires_at' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'ip_address' => $ipAddress,
        ]);

        // Send OTP email
        try {
            Mail::to($email)->send(new OtpMail($otpCode, $purpose));
        } catch (\Exception $e) {
            // Delete OTP record if email sending fails
            $otp->delete();
            return [
                'success' => false,
                'message' => 'Không thể gửi email. Vui lòng thử lại sau.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Mã OTP đã được gửi đến email của bạn',
            'expires_at' => $otp->expires_at->toIso8601String(),
        ];
    }

    /**
     * Verify OTP code
     *
     * @param string $email
     * @param string $otpCode
     * @param string $purpose
     * @return array
     */
    public function verifyOtp(string $email, string $otpCode, string $purpose = 'register'): array
    {
        $otp = Otp::forEmailAndPurpose($email, $purpose)
            ->where('otp_code', $otpCode)
            ->where('is_used', false)
            ->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'Mã OTP không hợp lệ',
            ];
        }

        if (!$otp->isValid()) {
            return [
                'success' => false,
                'message' => 'Mã OTP đã hết hạn',
            ];
        }

        // Mark OTP as used
        $otp->markAsUsed();

        return [
            'success' => true,
            'message' => 'Xác thực OTP thành công',
        ];
    }

    /**
     * Check if email exists
     *
     * @param string $email
     * @return array
     */
    public function checkEmailExists(string $email): array
    {
        $exists = Customer::where('email', $email)->whereNotNull('password')->exists();

        return [
            'success' => true,
            'exists' => $exists,
            'message' => $exists ? 'Email này đã được sử dụng' : 'Email có thể sử dụng',
        ];
    }

    /**
     * Generate random OTP code
     *
     * @return string
     */
    private function generateOtpCode(): string
    {
        return str_pad((string) random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }
}
