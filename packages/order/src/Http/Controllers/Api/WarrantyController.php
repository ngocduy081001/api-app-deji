<?php

namespace Vendor\Order\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\Order\Http\Resources\WarrantyResource;
use Vendor\Warranty\Models\Warranty;
use Vendor\Product\Models\Product;
use Vendor\Customer\Models\Customer;

class WarrantyController extends Controller
{
    /**
     * Display a listing of warranties.
     */
    public function index(Request $request)
    {
        $query = Warranty::with(['customer', 'product']);

        // Filter by status
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        // Filter by warranty_code
        if ($request->has('warranty_code') && $request->input('warranty_code') !== '') {
            $query->where('warranty_code', $request->input('warranty_code'));
        }

        // Filter by customer phone
        if ($request->has('phone') && $request->input('phone') !== '') {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('phone', $request->input('phone'));
            });
        }

        // Filter by product_id
        if ($request->has('product_id') && $request->input('product_id') !== '') {
            $query->where('product_id', $request->input('product_id'));
        }

        // Search
        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('warranty_code', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = $request->query('per_page', 20);
        $warranties = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => WarrantyResource::collection($warranties->items()),
            'pagination' => [
                'current_page' => $warranties->currentPage(),
                'last_page' => $warranties->lastPage(),
                'per_page' => $warranties->perPage(),
                'total' => $warranties->total(),
            ],
        ]);
    }

    /**
     * Display the specified warranty.
     */
    public function show($id)
    {
        $warranty = Warranty::with(['customer', 'product'])->findOrFail($id);

        // Format dates for response
        if ($warranty->active_date) {
            $warranty->active_date = \Carbon\Carbon::parse($warranty->active_date)->format('d/m/Y');
        }
        if ($warranty->time_expired) {
            $warranty->time_expired = \Carbon\Carbon::parse($warranty->time_expired)->format('d/m/Y');
        }

        return response()->json([
            'data' => new WarrantyResource($warranty),
        ]);
    }

    /**
     * Lookup warranty by code.
     */
    public function lookupByCode(Request $request)
    {
        $warrantyCode = $request->query('warranty_code');

        if (!$warrantyCode) {
            return response()->json([
                'error' => 'Warranty code not provided',
            ], 400);
        }

        $warranties = Warranty::where('warranty_code', $warrantyCode)
            ->with('customer', 'product')
            ->get();

        if ($warranties->isEmpty()) {
            // Try to find by customer phone
            $customer = Customer::where('phone', $warrantyCode)->first();
            if ($customer) {
                $warranties = Warranty::where('customer_id', $customer->id)
                    ->with('customer', 'product')
                    ->get();
            }
        }

        // Format dates
        foreach ($warranties as $warranty) {
            if ($warranty->active_date) {
                $warranty->active_date = \Carbon\Carbon::parse($warranty->active_date)->format('d/m/Y');
            }
            if ($warranty->time_expired) {
                $warranty->time_expired = \Carbon\Carbon::parse($warranty->time_expired)->format('d/m/Y');
            }
        }

        return response()->json([
            'data' => WarrantyResource::collection($warranties),
        ]);
    }

    /**
     * Lookup warranty by phone.
     */
    public function lookupByPhone(Request $request)
    {
        $phone = $request->query('phone');

        if (!$phone) {
            return response()->json([
                'error' => 'Phone number not provided',
            ], 400);
        }

        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json([
                'error' => 'Customer not found',
            ], 404);
        }

        $warranties = Warranty::where('customer_id', $customer->id)
            ->with('customer', 'product')
            ->get();

        // Format dates
        foreach ($warranties as $warranty) {
            if ($warranty->active_date) {
                $warranty->active_date = \Carbon\Carbon::parse($warranty->active_date)->format('d/m/Y');
            }
            if ($warranty->time_expired) {
                $warranty->time_expired = \Carbon\Carbon::parse($warranty->time_expired)->format('d/m/Y');
            }
        }

        return response()->json([
            'data' => WarrantyResource::collection($warranties),
        ]);
    }

    /**
     * Activate warranty.
     */
    public function activate(Request $request)
    {
        $validatedData = $request->validate([
            'warranty_code' => 'required|string',
            'active_date' => 'required|date',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email',
        ]);

        try {
            // Retrieve warranty by code
            $warranty = Warranty::where('warranty_code', $validatedData['warranty_code'])->first();

            if (!$warranty) {
                return response()->json([
                    'error' => 'Warranty not found',
                ], 404);
            }

            // Find or create customer
            $customer = Customer::where('phone', $validatedData['phone'])->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $validatedData['name'],
                    'phone' => $validatedData['phone'],
                    'email' => $validatedData['email'] ?? null,
                ]);
            }

            // Update warranty
            $warranty->active_date = $validatedData['active_date'];
            $warranty->customer_id = $customer->id;
            $warranty->status = 'active';
            $warranty->save();

            // Calculate expiration date
            if ($warranty->month) {
                $warranty->time_expired = \Carbon\Carbon::parse($warranty->active_date)
                    ->addMonthsNoOverflow($warranty->month)
                    ->format('Y-m-d');
            }

            $warranty->load('customer', 'product');

            // Format dates
            $warranty->time_expired = \Carbon\Carbon::parse($warranty->active_date)->addMonthsNoOverflow(12)->format('d/m/Y');
            $warranty->active_date = \Carbon\Carbon::parse($warranty->active_date)->format('d/m/Y');

            return response()->json([
                'message' => 'Kích hoạt bảo hành thành công!',
                'data' => new WarrantyResource($warranty),
                'has_active' => true,
                'customer' => $customer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi kích hoạt bảo hành.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified warranty.
     */
    public function update(Request $request, $id)
    {
        $warranty = Warranty::findOrFail($id);

        $validatedData = $request->validate([
            'status' => 'sometimes|string|in:clear,active,expired',
            'active_date' => 'sometimes|date',
            'time_expired' => 'sometimes|date',
            'customer_id' => 'sometimes|integer|exists:customers,id',
        ]);

        $warranty->update($validatedData);
        $warranty->load(['customer', 'product']);

        return response()->json([
            'message' => 'Warranty updated successfully.',
            'data' => new WarrantyResource($warranty),
        ]);
    }

    /**
     * Remove the specified warranty.
     */
    public function destroy($id)
    {
        $warranty = Warranty::findOrFail($id);
        $warranty->delete();

        return response()->json([
            'message' => 'Warranty deleted successfully.',
        ]);
    }
}

