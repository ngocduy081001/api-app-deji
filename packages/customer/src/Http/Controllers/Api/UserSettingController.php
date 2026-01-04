<?php

namespace Vendor\Customer\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Vendor\Customer\Models\CustomerSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserSettingController extends Controller
{
    /**
     * Get user settings.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Get or create settings with defaults
            $setting = CustomerSetting::getOrCreate($user->id);
            $settings = $setting->getAllSettings();

            return response()->json([
                'success' => true,
                'data' => $settings,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user settings.
     */
    public function update(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $validated = $request->validate([
                'settings' => 'required|array',
            ]);

            // Get or create settings
            $setting = CustomerSetting::getOrCreate($user->id);
            
            // Update settings
            $setting->setSettings($validated['settings']);
            $setting->save();

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'data' => $setting->getAllSettings(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating user settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
