<?php

namespace Vendor\Customer\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vendor\Customer\Models\Customer;

class ProfileController extends Controller
{
    /**
     * Get user profile by phone or email
     */
    public function show(Request $request)
    {
        try {
            $phone = $request->query('phone');
            $email = $request->query('email');
            $id = $request->query('id');

            $query = Customer::query();

            if ($id) {
                $customer = $query->find($id);
            } elseif ($phone) {
                $customer = $query->where('phone', $phone)->first();
            } elseif ($email) {
                $customer = $query->where('email', $email)->first();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone, email or id is required',
                ], 400);
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
     * Update user profile
     */
    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'sometimes|integer|exists:customers,id',
                'phone' => 'sometimes|string|max:15',
                'email' => 'sometimes|email|max:255',
                'name' => 'sometimes|string|max:255',
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

                if (!empty($updateData)) {
                    $customer->update($updateData);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
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

