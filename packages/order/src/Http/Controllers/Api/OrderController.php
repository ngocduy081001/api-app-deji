<?php

namespace Vendor\Order\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Vendor\Order\Http\Resources\OrderResource;
use Vendor\Order\Models\Order;
use Vendor\Order\Models\OrderItem;
use Vendor\Order\Models\CustomerAddress;
use Vendor\Product\Models\Product;
use Vendor\Customer\Models\Customer;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use Vendor\Settings\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'orderItems.product']);

        // Nếu user đã đăng nhập, mặc định lấy đơn hàng theo customer_id của user
        $user = $request->user();
        if ($user && $user->id) {
            // Nếu có customer_id trong request, ưu tiên dùng nó (cho admin)
            // Nếu không có, tự động filter theo customer_id của user đã đăng nhập
            if ($request->has('customer_id') && $request->input('customer_id') !== '') {
                $query->where('customer_id', $request->input('customer_id'));
            } else {
                // Tự động lấy đơn hàng theo customer_id của user đã đăng nhập
                $query->where('customer_id', $user->id);
            }
        } else {
            // Nếu chưa đăng nhập, chỉ cho phép tìm theo phone (backward compatibility)
            if ($request->has('phone') && $request->input('phone') !== '') {
                $query->where('customer_phone', $request->input('phone'));
            } else {
                // Nếu không có phone và không đăng nhập, trả về empty
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => $request->query('per_page', 20),
                        'total' => 0,
                    ],
                ]);
            }
        }

        // Filter by status
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        // Filter by payment_status
        if ($request->has('payment_status') && $request->input('payment_status') !== '') {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // Filter by customer phone (chỉ dùng khi chưa đăng nhập hoặc admin muốn tìm theo phone khác)
        if ($request->has('phone') && $request->input('phone') !== '' && !$user) {
            $query->where('customer_phone', $request->input('phone'));
        }

        // Filter by order_number
        if ($request->has('order_number') && $request->input('order_number') !== '') {
            $query->where('order_number', 'like', '%' . $request->input('order_number') . '%');
        }

        // Search
        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        try {
            $perPage = $request->query('per_page', 20);
            $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => OrderResource::collection($orders->items()),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching orders list: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching orders',
                'error' => $e->getMessage(),
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 20,
                    'total' => 0,
                ],
            ], 500);
        }
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Order creation request received', ['request_data' => $request->all()]);
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'required|string|max:15',
                'address' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'city' => 'nullable|string|max:255',
                'district' => 'required|string|max:255',
                'ward' => 'nullable|string|max:255',
                'note' => 'nullable|string|max:1000',
                'price' => 'required|numeric',
                'product' => 'required|array',
                'product.*.item.id' => 'required|integer|exists:products,id',
                'product.*.quantity' => 'required|integer|min:1',
                'payment' => 'required|string',
            ]);
            
            Log::info('Validation passed', ['validated_data' => $validatedData]);

            // Check if the customer already exists by phone
            $customer = Customer::where('phone', $validatedData['phone'])->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $validatedData['name'],
                    'phone' => $validatedData['phone'],
                    'email' => $validatedData['email'] ?? null,
                ]);
            }

            // Prepare metadata for ward (since orders table doesn't have ward field)
            $metadata = [];
            if (!empty($validatedData['ward'])) {
                $metadata['ward'] = $validatedData['ward'];
            }

            // Create the order
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => Order::generateOrderNumber(),
                'address' => $validatedData['address'],
                'province' => $validatedData['province'],
                'district' => $validatedData['district'],
                'city' => $validatedData['city'] ?? null,
                'payment_method' => $validatedData['payment'],
                'customer_name' => $validatedData['name'],
                'customer_email' => $validatedData['email'] ?? null,
                'customer_phone' => $validatedData['phone'],
                'customer_address' => $validatedData['address'],
                'subtotal' => $validatedData['price'],
                'tax' => $validatedData['price'] * 0.1,
                'total' => $validatedData['price'] + ($validatedData['price'] * 0.1),
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_STATUS_PENDING,
                'notes' => $validatedData['note'] ?? null,
                'metadata' => !empty($metadata) ? $metadata : null,
            ]);

            // Create order items
            foreach ($validatedData['product'] as $productData) {
                $product = Product::find($productData['item']['id']);
                if (!$product) {
                    throw new \Exception("Product with ID {$productData['item']['id']} not found.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'price' => $product->price,
                    'total' => $product->price * $productData['quantity'],
                ]);

                $productData['item']['name'] = $product->name;
            }

            // Send notification email (wrap in try-catch to not block order creation)
            try {
                $this->sendOrderNotificationEmail($order, $validatedData);
            } catch (\Exception $e) {
                Log::error('Error sending order notification email: ' . $e->getMessage());
            }

            // Create notification (wrap in try-catch to not block order creation)
            try {
                $link = null;
                try {
                    $link = route('admin.orders.show', $order);
                } catch (\Exception $e) {
                    Log::warning('Could not generate route for notification: ' . $e->getMessage());
                }
                
                Notification::create([
                    'type' => 'order',
                    'title' => 'Đơn hàng mới',
                    'message' => "Có đơn hàng mới từ khách hàng {$customer->name} - Mã đơn: {$order->order_number}",
                    'link' => $link,
                    'is_read' => false,
                ]);
            } catch (\Exception $e) {
                Log::error('Error creating notification: ' . $e->getMessage());
            }

            // Lưu thông tin địa chỉ giao hàng để sử dụng lần sau
            try {
                CustomerAddress::updateOrCreate(
                    [
                        'phone' => $validatedData['phone'],
                        'province' => $validatedData['province'],
                        'district' => $validatedData['district'],
                        'ward' => $validatedData['ward'] ?? '',
                        'address' => $validatedData['address'],
                    ],
                    [
                        'customer_id' => $customer->id,
                        'name' => $validatedData['name'],
                        'email' => $validatedData['email'] ?? null,
                        'note' => $validatedData['note'] ?? null,
                        'last_used_at' => now(),
                    ]
                );
            } catch (\Exception $e) {
                // Log error nhưng không block order flow
                Log::error('Error saving customer address: ' . $e->getMessage());
            }

            $order->load(['customer', 'orderItems.product']);

            // Return response format compatible with frontend (expects Cart object)
            return response()->json([
                'message' => 'Bạn đã đặt hàng thành công ! Kiểm tra email của bạn để xem thông tin chi tiết.',
                'Cart' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'total' => $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at?->format('Y-m-d H:i:s'),
                ],
                'data' => new OrderResource($order),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in order creation', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating order', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Display the specified order.
     * Can find by id (numeric) or order_number (string)
     */
    public function show($id)
    {
        try {
            // Try to find by id first (if numeric)
            if (is_numeric($id)) {
                $order = Order::with(['customer', 'orderItems.product'])->find($id);
            } else {
                // If not numeric, try to find by order_number
                $order = Order::with(['customer', 'orderItems.product'])
                    ->where('order_number', $id)
                    ->first();
            }

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new OrderResource($order),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching order detail',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validatedData = $request->validate([
            'status' => 'sometimes|string|in:pending,processing,shipped,delivered,cancelled,completed',
            'payment_status' => 'sometimes|string|in:pending,paid,failed,refunded,success',
            'notes' => 'nullable|string',
        ]);

        $order->update($validatedData);
        $order->load(['customer', 'orderItems.product']);

        return response()->json([
            'message' => 'Order updated successfully.',
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * Remove the specified order.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully.',
        ]);
    }

    /**
     * Send order notification email.
     */
    protected function sendOrderNotificationEmail(Order $order, array $orderData): void
    {
        $settingsKeys = [
            'email_driver',
            'email_hostName',
            'email_encryption',
            'email_port',
            'email_userName',
            'email_password',
            'email_senderName',
            'email_senderEmail',
            'admin_email',
            'Logo',
        ];

        $configurations = Setting::whereIn('key', $settingsKeys)->pluck('value', 'key');

        Config::set([
            'mail.mailers.smtp.host' => $configurations['email_hostName'] ?? config('mail.mailers.smtp.host'),
            'mail.mailers.smtp.port' => $configurations['email_port'] ?? config('mail.mailers.smtp.port'),
            'mail.mailers.smtp.encryption' => $configurations['email_encryption'] ?? config('mail.mailers.smtp.encryption'),
            'mail.mailers.smtp.username' => $configurations['email_userName'] ?? config('mail.mailers.smtp.username'),
            'mail.mailers.smtp.password' => $configurations['email_password'] ?? config('mail.mailers.smtp.password'),
            'mail.from.address' => $configurations['email_senderEmail'] ?? config('mail.from.address'),
            'mail.from.name' => $configurations['email_senderName'] ?? config('mail.from.name'),
        ]);

        $details = [
            'return' => 'Order',
            'title' => 'Deji Việt Nam',
            'body' => 'Thông tin đơn hàng của khách hàng',
            'logo' => $configurations['Logo'] ?? null,
            'customer' => $order->customer,
            'product' => $orderData['product'],
            'order' => $order,
        ];

        $ccEmails = [];
        if (!empty($order->customer_email) && filter_var($order->customer_email, FILTER_VALIDATE_EMAIL)) {
            $ccEmails[] = $order->customer_email;
        }

        // Send order email to admin_email from settings
        if (!empty($configurations['admin_email']) && filter_var($configurations['admin_email'], FILTER_VALIDATE_EMAIL)) {
            Mail::to($configurations['admin_email'])
                ->cc($ccEmails)
                ->send(new ContactFormMail($details));
        }

        // Send order email to MAIL_ADMIN from env
        $mailAdmin = env('MAIL_ADMIN');
        if (!empty($mailAdmin) && filter_var($mailAdmin, FILTER_VALIDATE_EMAIL)) {
            Mail::to($mailAdmin)
                ->cc($ccEmails)
                ->send(new ContactFormMail($details));
        }
    }
}

