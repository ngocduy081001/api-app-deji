<?php

namespace Vendor\Order\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\Order\Http\Resources\BookingResource;
use Vendor\Order\Models\Booking;
use Vendor\Order\Models\Showroom;
use Vendor\Product\Models\Product;
use Vendor\Customer\Models\Customer;
use App\Models\Notification;
use Vendor\Order\Http\Requests\StoreBookingRequest;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['customer', 'product', 'showroom']);

        // Filter by status
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        // Filter by date
        if ($request->has('date') && $request->input('date') !== '') {
            $query->whereDate('date', $request->input('date'));
        }

        // Filter by customer phone
        if ($request->has('phone') && $request->input('phone') !== '') {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('phone', $request->input('phone'));
            });
        }

        // Search
        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('showroom', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $perPage = $request->query('per_page', 20);
        $bookings = $query->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => BookingResource::collection($bookings->items()),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    /**
     * Store a newly created booking.
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        try {
            $customer = $this->resolveCustomer($validated);

            $booking = Booking::create([
                'customer_id' => $customer->id,
                'showroom_id' => $validated['showroom_id'],
                'product_id' => $validated['product_id'],
                'price' => $validated['price'] == 'Liên hệ' ? 0 : $validated['price'],
                'date' => $validated['date'],
                'time' => $validated['time'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $showroom = Showroom::findOrFail($validated['showroom_id']);
            $product = Product::findOrFail($validated['product_id']);

            // Create notification for new booking
            Notification::create([
                'type' => 'booking',
                'title' => 'Booking mới',
                'message' => "Có booking mới từ khách hàng {$customer->name} - Ngày: {$booking->date->format('d/m/Y')} {$booking->time}",
                'link' => route('admin.bookings.index'),
                'is_read' => false,
            ]);

            $booking->load(['customer', 'product', 'showroom']);

            return response()->json([
                'message' => 'Bạn đã tạo lịch hẹn thành công ! Kiểm tra email của bạn để xem thông tin chi tiết.',
                'data' => new BookingResource($booking),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified booking.
     */
    public function show($id)
    {
        $booking = Booking::with(['customer', 'product', 'showroom'])->findOrFail($id);

        return response()->json([
            'data' => new BookingResource($booking),
        ]);
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validatedData = $request->validate([
            'status' => 'sometimes|string',
            'date' => 'sometimes|date',
            'time' => 'sometimes|string',
            'notes' => 'nullable|string',
        ]);

        $booking->update($validatedData);
        $booking->load(['customer', 'product', 'showroom']);

        return response()->json([
            'message' => 'Booking updated successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

    /**
     * Remove the specified booking.
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json([
            'message' => 'Booking deleted successfully.',
        ]);
    }

    /**
     * Find or create customer by phone number.
     */
    protected function resolveCustomer(array $data): Customer
    {
        $customer = Customer::firstOrNew(['phone' => $data['phone']]);

        $customer->fill([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
        ])->save();

        return $customer;
    }
}

