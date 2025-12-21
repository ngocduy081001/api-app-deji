<?php

namespace Vendor\Order\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Vendor\Customer\Models\Customer;
use Vendor\Order\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {

        try {
            $query = Order::query();

            // Search by order number, customer name, email or phone
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status') && $request->input('status') !== '') {
                $query->where('status', $request->input('status'));
            }

            // Filter by appointment status
            if ($request->has('appointment_status') && $request->input('appointment_status') !== '') {
                $query->where('appointment_status', $request->input('appointment_status'));
            }

            // Filter by appointment date
            if ($request->has('appointment_date') && $request->input('appointment_date') !== '') {
                $query->whereDate('appointment_date', $request->input('appointment_date'));
            }

            $orders = $query->orderBy('id', 'desc')->paginate(20);

            return view('order::admin.index', compact('orders'));
        } catch (\Exception $e) {

            // Log error and redirect with error message instead of silently redirecting
            Log::error('Error loading orders: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Có lỗi xảy ra khi tải danh sách đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('order::admin.create', compact('customers'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping_fee' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
            'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled',
            'appointment_date' => 'nullable|date',
            'appointment_time' => 'nullable|date_format:H:i',
            'appointment_note' => 'nullable|string',
            'appointment_status' => 'nullable|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $validated['order_number'] = Order::generateOrderNumber();

        $order = Order::create($validated);

        // Send email notification to admin
        $this->sendOrderNotificationEmail($order);

        // Create notification for new order
        Notification::create([
            'type' => 'order',
            'title' => 'Đơn hàng mới',
            'message' => "Có đơn hàng mới từ khách hàng {$order->customer_name} - Mã đơn: {$order->order_number}",
            'link' => route('admin.orders.show', $order),
            'is_read' => false,
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Đơn hàng đã được tạo thành công.');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'customer']);
        return view('order::admin.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $customers = Customer::orderBy('name')->get();
        return view('order::admin.edit', compact('order', 'customers'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping_fee' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
            'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled',
            'appointment_date' => 'nullable|date',
            'appointment_time' => 'nullable|date_format:H:i',
            'appointment_note' => 'nullable|string',
            'appointment_status' => 'nullable|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Đơn hàng đã được cập nhật thành công.');
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Đơn hàng đã được xóa thành công.');
    }

    /**
     * Send order notification email to admin.
     */
    protected function sendOrderNotificationEmail(Order $order): void
    {
        try {
            $adminEmail = env('MAIL_ADMIN');

            if (!$adminEmail || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                Log::warning('MAIL_ADMIN is not set or invalid, skipping order notification email');
                return;
            }

            // Load order items if available
            $order->load('orderItems.product');

            $details = [
                'return' => 'Order',
                'title' => config('app.name', 'Deji Việt Nam'),
                'body' => 'Có đơn hàng mới được tạo',
                'order' => [
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'customer_phone' => $order->customer_phone,
                    'total' => number_format($order->total, 0, ',', '.') . ' đ',
                    'payment_method' => $order->payment_method ?? 'N/A',
                    'payment_status' => $order->payment_status ?? 'pending',
                    'status' => $order->status ?? 'pending',
                    'province' => $order->province ?? '',
                    'city' => $order->city ?? '',
                    'district' => $order->district ?? '',
                    'address' => $order->address ?? $order->customer_address ?? '',
                    'appointment_date' => $order->appointment_date ? $order->appointment_date->format('d/m/Y') : null,
                    'appointment_time' => $order->appointment_time ? (is_string($order->appointment_time) ? $order->appointment_time : $order->appointment_time->format('H:i')) : null,
                    'notes' => $order->notes ?? '',
                ],
                'customer' => [
                    'name' => $order->customer_name,
                    'email' => $order->customer_email ?? 'N/A',
                    'phone' => $order->customer_phone,
                ],
                'product' => $order->orderItems->map(function ($item) {
                    return [
                        'item' => [
                            'name' => $item->product->name ?? 'N/A',
                        ],
                        'quantity' => $item->quantity,
                        'price' => number_format($item->price, 0, ',', '.') . ' đ',
                        'total' => number_format($item->total, 0, ',', '.') . ' đ',
                    ];
                })->toArray(),
            ];

            Mail::to($adminEmail)
                ->send(new ContactFormMail($details));
        } catch (\Exception $e) {
            Log::error('Failed to send order notification email: ' . $e->getMessage(), [
                'exception' => $e,
                'order_id' => $order->id,
            ]);
        }
    }
}
