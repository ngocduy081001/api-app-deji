<?php

namespace Vendor\Warranty\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Vendor\Customer\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Vendor\Warranty\Http\Requests\ClaimWarrantyRequest;
use Vendor\Warranty\Models\Warranty;

class WarrantyClaimController extends Controller
{
    /**
     * Show claim form when scanning QR.
     */
    public function show(string $code): View
    {
        $warranty = Warranty::with('product', 'customer')
            ->where('warranty_code', $code)
            ->firstOrFail();

        return view('warranty::public.claim', [
            'warranty' => $warranty,
        ]);
    }

    /**
     * Handle warranty activation submitted by customer.
     */
    public function store(ClaimWarrantyRequest $request, string $code): RedirectResponse
    {
        $warranty = Warranty::with('product')
            ->where('warranty_code', $code)
            ->firstOrFail();

        if ($warranty->status === 'expired') {
            return back()->with('error', 'Mã bảo hành này đã hết hạn.')->withInput();
        }

        if ($warranty->status === 'active') {
            return back()->with('error', 'Mã bảo hành này đã được kích hoạt.')->withInput();
        }

        $data = $request->validated();

        $customer = $this->resolveCustomer($data);

        $activeDate = Carbon::now();
        $months = $warranty->month ?: config('warranty.default_months', 12);

        $warranty->update([
            'status' => 'active',
            'active_date' => $activeDate,
            'time_expired' => (clone $activeDate)->addMonths($months),
            'customer_id' => $customer?->id,
        ]);

        return back()->with('success', 'Bạn đã kích hoạt bảo hành thành công!');
    }

    protected function resolveCustomer(array $data): ?Customer
    {
        if (!empty($data['customer_email'])) {
            return Customer::firstOrCreate(
                ['email' => $data['customer_email']],
                [
                    'name' => $data['customer_name'],
                    'phone' => $data['customer_phone'],
                ]
            );
        }

        return Customer::firstOrCreate(
            ['phone' => $data['customer_phone']],
            [
                'name' => $data['customer_name'],
                'email' => $data['customer_email'],
            ]
        );
    }
}
