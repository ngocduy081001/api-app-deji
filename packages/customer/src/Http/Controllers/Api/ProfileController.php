<?php

namespace Vendor\Customer\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Vendor\Customer\Models\Customer;

class ProfileController extends Controller
{
    /**
     * Get user profile by phone, email, id, or authenticated user
     */
    public function show(Request $request)
    {
        try {
            $phone = $request->query('phone');
            $email = $request->query('email');
            $id = $request->query('id');

            $customer = null;

            // Priority: authenticated user > id > phone > email
            if ($request->user()) {
                $customer = $request->user();
            } elseif ($id) {
                $customer = Customer::find($id);
            } elseif ($phone) {
                $customer = Customer::where('phone', $phone)->first();
            } elseif ($email) {
                $customer = Customer::where('email', $email)->first();
            }

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'avatar' => $this->getAvatarUrl($customer),
                    'created_at' => $customer->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $customer->updated_at?->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get avatar URL for customer
     */
    protected function getAvatarUrl(Customer $customer): ?string
    {
        if (!$customer->avatar) {
            return null;
        }

        // If avatar is already a full URL, return as is
        if (filter_var($customer->avatar, FILTER_VALIDATE_URL)) {
            return $customer->avatar;
        }

        // Return storage URL
        return asset('storage/' . $customer->avatar);
    }

    /**
     * Update user profile
     * Supports avatar upload via multipart/form-data
     */
    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'sometimes|integer|exists:customers,id',
                'phone' => 'sometimes|string|max:15',
                'email' => 'sometimes|email|max:255',
                'name' => 'sometimes|string|max:255',
                'avatar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
            ]);

            $customer = null;

            // Find customer by id, phone, or email
            if (isset($validatedData['id'])) {
                $customer = Customer::find($validatedData['id']);
            } elseif (isset($validatedData['phone'])) {
                $customer = Customer::where('phone', $validatedData['phone'])->first();
            } elseif ($request->has('phone')) {
                $customer = Customer::where('phone', $request->input('phone'))->first();
            } elseif ($request->has('email')) {
                $customer = Customer::where('email', $request->input('email'))->first();
            } elseif ($request->user()) {
                // If authenticated, use authenticated user
                $customer = $request->user();
            }

            // If customer not found, create new one
            if (!$customer) {
                if (!isset($validatedData['phone']) && !$request->has('phone')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Phone is required to create new customer',
                    ], 400);
                }

                $customer = Customer::create([
                    'name' => $validatedData['name'] ?? 'Customer',
                    'email' => $validatedData['email'] ?? null,
                    'phone' => $validatedData['phone'] ?? $request->input('phone'),
                ]);
            } else {
                // Update existing customer
                $updateData = [];
                if (isset($validatedData['name'])) {
                    $updateData['name'] = $validatedData['name'];
                }
                if (isset($validatedData['email'])) {
                    $updateData['email'] = $validatedData['email'];
                }
                if (isset($validatedData['phone'])) {
                    $updateData['phone'] = $validatedData['phone'];
                }

                // Handle avatar upload
                if ($request->hasFile('avatar')) {
                    $file = $request->file('avatar');

                    // Delete old avatar if exists
                    if ($customer->avatar && Storage::disk('public')->exists($customer->avatar)) {
                        Storage::disk('public')->delete($customer->avatar);
                    }

                    // Generate unique filename
                    $filename = 'avatars/' . $customer->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                    // Store file in public disk
                    $path = $file->storeAs('avatars', $customer->id . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');

                    $updateData['avatar'] = $path;
                }

                if (!empty($updateData)) {
                    $customer->update($updateData);
                }
            }

            // Refresh customer to get updated data
            $customer->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'avatar' => $this->getAvatarUrl($customer),
                    'created_at' => $customer->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $customer->updated_at?->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload avatar only
     * 
     * Dedicated endpoint for avatar upload - makes it clear this is for image upload.
     * Mobile app should use this endpoint when user clicks on avatar button.
     * 
     * MOBILE APP IMPLEMENTATION GUIDE:
     * 
     * 1. Request Permission (when app opens, if not granted):
     *    - iOS: Request PHPhotoLibrary authorization
     *    - Android: Request READ_MEDIA_IMAGES permission (Android 13+) or READ_EXTERNAL_STORAGE
     * 
     * 2. Show Avatar as Clickable Button:
     *    - Display avatar image with tap/click handler
     *    - Add visual indicator (border, shadow, icon overlay) to show it's clickable
     *    - Show "Thay đổi ảnh đại diện" text below avatar
     * 
     * 3. On Avatar Click:
     *    - Open image picker (camera or gallery)
     *    - User selects/captures image
     *    - Upload to this endpoint using FormData
     * 
     * 4. Upload Request:
     *    POST /api/profile/avatar
     *    Headers: Authorization: Bearer {access_token}
     *    Body: multipart/form-data
     *      - avatar: (file)
     * 
     * 5. Response:
     *    {
     *      "success": true,
     *      "message": "Avatar uploaded successfully",
     *      "data": {
     *        "avatar": "http://domain.com/storage/avatars/1_1234567890.jpg"
     *      }
     *    }
     */
    public function uploadAvatar(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
            ]);

            $customer = $request->user();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please login first.',
                ], 401);
            }

            $file = $request->file('avatar');

            // Delete old avatar if exists
            if ($customer->avatar && Storage::disk('public')->exists($customer->avatar)) {
                Storage::disk('public')->delete($customer->avatar);
            }

            // Generate unique filename: avatars/{customer_id}_{timestamp}.{ext}
            $filename = $customer->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');

            // Update customer avatar
            $customer->update(['avatar' => $path]);
            $customer->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'data' => [
                    'avatar' => $this->getAvatarUrl($customer),
                    'avatar_path' => $path, // For reference
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading avatar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading avatar',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete avatar
     * Allows user to remove their avatar
     */
    public function deleteAvatar(Request $request)
    {
        try {
            $customer = $request->user();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please login first.',
                ], 401);
            }

            // Delete file from storage
            if ($customer->avatar && Storage::disk('public')->exists($customer->avatar)) {
                Storage::disk('public')->delete($customer->avatar);
            }

            // Remove avatar from database
            $customer->update(['avatar' => null]);
            $customer->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Avatar deleted successfully',
                'data' => [
                    'avatar' => null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting avatar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting avatar',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create or get customer profile
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'email' => 'nullable|email|max:255',
            ]);

            // Check if customer already exists by phone
            $customer = Customer::where('phone', $validatedData['phone'])->first();

            if ($customer) {
                // Update existing customer
                $customer->update([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'] ?? $customer->email,
                ]);
            } else {
                // Create new customer
                $customer = Customer::create($validatedData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile created successfully',
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'created_at' => $customer->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $customer->updated_at?->format('Y-m-d H:i:s'),
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
