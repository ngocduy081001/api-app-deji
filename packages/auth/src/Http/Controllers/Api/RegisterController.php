<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Vendor\Customer\Models\Customer;
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
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'scope' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
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
            ]);
        } else {
            // Create new customer
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone ?? null,
            ]);
            $isNewCustomer = true;
        }

        $tokenResponse = $this->passportTokenService->issuePasswordToken(
            $request->email,
            $request->password,
            $this->resolveScope($request)
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

    protected function resolveScope(Request $request): string
    {
        return $request->input('scope', config('services.passport.default_scope', ''));
    }
}
