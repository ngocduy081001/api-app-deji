<?php

namespace Vendor\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Vendor\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Vendor\Auth\Services\PassportTokenService;

class GoogleLoginController extends Controller
{
    public function __construct(
        protected PassportTokenService $passportTokenService,
    ) {}

    /**
     * Login or register user with Google ID token
     * 
     * Flow:
     * 1. Frontend gửi Google ID token
     * 2. Backend verify token với Google
     * 3. Tạo hoặc cập nhật user trong database
     * 4. Trả về JWT token (Laravel Passport)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string', // Google ID token
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verify Google ID token
            $googleUser = $this->verifyGoogleToken($request->id_token);

            if (!$googleUser) {
                return response()->json([
                    'message' => 'Invalid Google token'
                ], 401);
            }

            // Extract user info from Google token
            $googleId = $googleUser['sub'] ?? null;
            $email = $googleUser['email'] ?? null;
            $name = $googleUser['name'] ?? null;
            $picture = $googleUser['picture'] ?? null;

            if (!$email) {
                return response()->json([
                    'message' => 'Email is required from Google account'
                ], 400);
            }

            // Find or create customer
            $customer = Customer::where('email', $email)->first();
            $isNewUser = false;
            $originalPassword = null;
            $tempPassword = null;

            if ($customer) {
                // Update existing customer with Google info
                if (!$customer->google_id && $googleId) {
                    $customer->google_id = $googleId;
                }
                if (!$customer->avatar && $picture) {
                    $customer->avatar = $picture;
                }
                if (!$customer->name && $name) {
                    $customer->name = $name;
                }

                // Handle password for token generation
                if (!$customer->password) {
                    // Google-only customer: create permanent password
                    $tempPassword = str()->random(32);
                    $customer->password = bcrypt($tempPassword);
                    $customer->save();
                } else {
                    // Customer has password: temporarily change for token generation
                    $originalPassword = $customer->password;
                    $tempPassword = str()->random(32);
                    $customer->password = bcrypt($tempPassword);
                    $customer->save();
                }
            } else {
                // Create new customer
                $tempPassword = str()->random(32);
                $customer = Customer::create([
                    'name' => $name ?? 'Google User',
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $picture,
                    'password' => bcrypt($tempPassword),
                    'email_verified_at' => now(), // Google emails are verified
                ]);
                $isNewUser = true;
            }

            // Generate JWT token using Passport password grant
            $tokenResponse = $this->passportTokenService->issuePasswordToken(
                $customer->email,
                $tempPassword,
                $request->input('scope', ''),
                $request->input('client_id'),
                $request->input('client_secret'),
            );

            // Restore original password if we temporarily changed it
            if ($originalPassword && $customer && !$isNewUser) {
                $customer->password = $originalPassword;
                $customer->save();
            }

            if ($tokenResponse['status'] !== 200) {
                return response()->json([
                    'message' => 'Unable to issue access token',
                    'errors' => $tokenResponse['data']['errors'] ?? null,
                ], $tokenResponse['status']);
            }

            return response()->json([
                'message' => $isNewUser ? 'Registration and login successful' : 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'avatar' => $customer->avatar,
                    ],
                    'access_token' => $tokenResponse['data']['access_token'] ?? null,
                    'refresh_token' => $tokenResponse['data']['refresh_token'] ?? null,
                    'token_type' => $tokenResponse['data']['token_type'] ?? 'Bearer',
                    'expires_in' => $tokenResponse['data']['expires_in'] ?? null,
                    'expires_at' => now()->addSeconds($tokenResponse['data']['expires_in'] ?? 0)->toIso8601String(),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => 'An error occurred during Google login',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Verify Google ID token with Google's tokeninfo endpoint
     * 
     * @param string $idToken Google ID token
     * @return array|null User info from Google or null if invalid
     */
    private function verifyGoogleToken(string $idToken): ?array
    {
        try {
            $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $idToken
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Verify the token is for our app (optional but recommended)
                // You can check 'aud' field matches your Google Client ID

                return $data;
            }

            Log::warning('Google token verification failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Google token verification exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Alternative method: Login with Google user info directly
     * Use this if frontend gets user info from Google API
     */
    public function loginWithUserInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_id' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'picture' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find or create customer
            $customer = Customer::where('email', $request->email)
                ->orWhere('google_id', $request->google_id)
                ->first();

            if ($customer) {
                // Update existing customer
                if (!$customer->google_id) {
                    $customer->google_id = $request->google_id;
                }
                if (!$customer->avatar && $request->picture) {
                    $customer->avatar = $request->picture;
                }
                if (!$customer->name && $request->name) {
                    $customer->name = $request->name;
                }
                $customer->save();
            } else {
                // Create new customer
                $customer = Customer::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'google_id' => $request->google_id,
                    'avatar' => $request->picture,
                    'password' => bcrypt(str()->random(32)),
                    'email_verified_at' => now(),
                ]);
            }

            // Generate token using password grant
            // Create temporary password for token generation
            $tempPassword = str()->random(32);
            $originalPassword = $customer->password;

            if (!$customer->password) {
                $customer->password = bcrypt($tempPassword);
                $customer->save();
            } else {
                $customer->password = bcrypt($tempPassword);
                $customer->save();
            }

            $tokenResponse = $this->passportTokenService->issuePasswordToken(
                $customer->email,
                $tempPassword,
                $request->input('scope', ''),
                $request->input('client_id'),
                $request->input('client_secret'),
            );

            // Restore original password if exists
            if ($originalPassword) {
                $customer->password = $originalPassword;
                $customer->save();
            }

            if ($tokenResponse['status'] !== 200) {
                return response()->json([
                    'message' => 'Unable to issue access token',
                    'errors' => $tokenResponse['data']['errors'] ?? null,
                ], $tokenResponse['status']);
            }

            return response()->json([
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'avatar' => $customer->avatar,
                    ],
                    'access_token' => $tokenResponse['data']['access_token'] ?? null,
                    'refresh_token' => $tokenResponse['data']['refresh_token'] ?? null,
                    'token_type' => $tokenResponse['data']['token_type'] ?? 'Bearer',
                    'expires_in' => $tokenResponse['data']['expires_in'] ?? null,
                    'expires_at' => now()->addSeconds($tokenResponse['data']['expires_in'] ?? 0)->toIso8601String(),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Google login with user info error: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred during Google login',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
