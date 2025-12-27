<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApi
{
    protected JwtTokenService $tokenService;

    public function __construct(JwtTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Missing or invalid Authorization header.',
            ], 401);
        }

        $token = substr($authHeader, 7); // Remove "Bearer " prefix

        // Validate token
        $payload = $this->tokenService->validateAccessToken($token);

        if (!$payload) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or expired token.',
            ], 401);
        }

        // Find user
        $user = User::find($payload['user_id']);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. User not found.',
            ], 401);
        }

        // Attach user to request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
