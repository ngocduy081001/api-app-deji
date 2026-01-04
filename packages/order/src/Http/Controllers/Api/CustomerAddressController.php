<?php

namespace Vendor\Order\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Vendor\Order\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CustomerAddressController extends Controller
{
    /**
     * Display a listing of saved addresses by phone number.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $phone = $request->query('phone');

            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số điện thoại là bắt buộc',
                ], 400);
            }

            $addresses = CustomerAddress::byPhone($phone)
                ->orderByLastUsed()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $addresses,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách địa chỉ',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created address.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'email' => 'nullable|email|max:255',
                'address' => 'required|string',
                'province' => 'required|string|max:255',
                'district' => 'required|string|max:255',
                'ward' => 'required|string|max:255',
                'note' => 'nullable|string',
                'customer_id' => 'nullable|exists:customers,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            // Tìm hoặc tạo customer nếu có customer_id
            $customerId = $data['customer_id'] ?? null;
            if (!$customerId) {
                $customer = \Vendor\Customer\Models\Customer::where('phone', $data['phone'])->first();
                if ($customer) {
                    $customerId = $customer->id;
                }
            }

            // Kiểm tra xem đã có address với thông tin tương tự chưa
            $existingAddress = CustomerAddress::byPhone($data['phone'])
                ->where('province', $data['province'])
                ->where('district', $data['district'])
                ->where('ward', $data['ward'])
                ->where('address', $data['address'])
                ->first();

            if ($existingAddress) {
                // Cập nhật thời gian sử dụng
                $existingAddress->update([
                    'last_used_at' => now(),
                    'name' => $data['name'],
                    'email' => $data['email'] ?? $existingAddress->email,
                    'note' => $data['note'] ?? $existingAddress->note,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Địa chỉ đã được cập nhật',
                    'data' => $existingAddress->fresh(),
                ], 200);
            }

            // Tạo address mới
            $address = CustomerAddress::create([
                'customer_id' => $customerId,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'],
                'province' => $data['province'],
                'district' => $data['district'],
                'ward' => $data['ward'],
                'note' => $data['note'] ?? null,
                'last_used_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Địa chỉ đã được lưu thành công',
                'data' => $address,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu địa chỉ',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified address.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $address = CustomerAddress::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $address,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy địa chỉ',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $address = CustomerAddress::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|required|string|max:15',
                'email' => 'nullable|email|max:255',
                'address' => 'sometimes|required|string',
                'province' => 'sometimes|required|string|max:255',
                'district' => 'sometimes|required|string|max:255',
                'ward' => 'sometimes|required|string|max:255',
                'note' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $address->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Địa chỉ đã được cập nhật',
                'data' => $address->fresh(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật địa chỉ',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified address.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $address = CustomerAddress::findOrFail($id);
            $address->delete();

            return response()->json([
                'success' => true,
                'message' => 'Địa chỉ đã được xóa',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa địa chỉ',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

