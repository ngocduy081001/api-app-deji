<?php

namespace Vendor\Order\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Vendor\Customer\Models\Customer;
use Vendor\Order\Http\Requests\StoreBookingRequest;
use Vendor\Order\Models\Booking;
use Vendor\Order\Models\Showroom;
use Vendor\Product\Models\Product;
use Vendor\Settings\Models\Setting;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['customer', 'product', 'showroom']);

        // Search
        if ($search = $request->get('search')) {
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter by date
        if ($date = $request->get('date')) {
            $query->whereDate('date', $date);
        }

        $bookings = $query->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(20);

        return view('order::admin.bookings.index', compact('bookings'));
    }

    /**
     * Handle booking creation from public API.
     */
    public function store(StoreBookingRequest $request): JsonResponse
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

            if (!empty($validated['email'])) {
                $this->sendNotificationEmails($customer, $booking, $showroom, $product, $validated['product']);
            }

            return response()->json([
                'message' => 'Bạn đã tạo lịch hẹn thành công ! Kiểm tra email của bạn để xem thông tin chi tiết.',
                'booking' => $booking->load('customer', 'product', 'showroom'),
                'customer' => $customer,
                'product' => $validated['product'],
                'showroom' => $showroom,
            ], 201);
        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                'error' => $th->getMessage(),
            ], 500);
        }
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

    /**
     * Send notification emails to admin, customer, and showroom.
     */
    protected function sendNotificationEmails(Customer $customer, Booking $booking, Showroom $showroom, Product $product, $productLabel): void
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
            'return' => 'Booking',
            'title' => 'Deji Việt Nam',
            'body' => 'Thông tin đặt hẹn của khách hàng',
            'logo' => $configurations['Logo'] ?? null,
            'customer' => $customer,
            'product' => $productLabel,
            'booking' => $booking,
            'showroom' => $showroom,
        ];

        $ccEmails = [];

        if (!empty($customer->email) && filter_var($customer->email, FILTER_VALIDATE_EMAIL)) {
            $ccEmails[] = $customer->email;
        }

        if (!empty($showroom->email) && filter_var($showroom->email, FILTER_VALIDATE_EMAIL)) {
            $ccEmails[] = $showroom->email;
        }

        // Add MAIL_ADMIN to CC
        $mailAdmin = env('MAIL_ADMIN');
        if (!empty($mailAdmin) && filter_var($mailAdmin, FILTER_VALIDATE_EMAIL)) {
            $ccEmails[] = $mailAdmin;
        }

        $adminEmail = $configurations['admin_email'] ?? null;

        if (!$adminEmail || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Admin email is invalid');
        }

        Mail::to($adminEmail)
            ->cc($ccEmails)
            ->send(new ContactFormMail($details));
    }
}
