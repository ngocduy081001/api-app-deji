<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Vendor\Auth\Services\PassportTokenService;

class RegisterController extends Controller
{
    public function __construct(
        protected PassportTokenService $passportTokenService
    ) {
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'scope' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $tokenResponse = $this->passportTokenService->issuePasswordToken(
            $request->email,
            $request->password,
            $this->resolveScope($request)
        );

        if ($tokenResponse['status'] !== 200) {
            return response()->json([
                'success' => false,
                'message' => $tokenResponse['data']['message'] ?? 'Unable to issue access token',
                'errors' => $tokenResponse['data']['errors'] ?? null,
            ], $tokenResponse['status']);
        }

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
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
        ], 201);
    }

    protected function resolveScope(Request $request): string
    {
        return $request->input('scope', config('services.passport.default_scope', ''));
    }
}
