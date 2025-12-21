<?php

namespace Vendor\Customer\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Vendor\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vendor\Customer\Http\Requests\StoreCustomerRequest;
use Vendor\Customer\Http\Requests\UpdateCustomerRequest;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search by name, email or phone
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(config('customer.per_page', 20));

        return view('customer::admin.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customer::admin.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        $customer = Customer::create($data);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Khách hàng đã được tạo thành công.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        return view('customer::admin.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        return view('customer::admin.edit', compact('customer'));
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();

        $customer->update($data);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Khách hàng đã được cập nhật thành công.');
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Khách hàng đã được xóa thành công.');
    }

    /**
     * Export customers to CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Customer::query();

        // Apply same filters as index
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->get();

        $filename = 'customers_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Add BOM for UTF-8
        $callback = function () use ($customers) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Add CSV headers
            fputcsv($file, [
                'STT',
                'Tên khách hàng',
                'Email',
                'Số điện thoại',
                'Ngày tạo',
                'Ngày cập nhật',
            ], ';');

            // Add data rows
            $index = 1;
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $index++,
                    $customer->name,
                    $customer->email,
                    $customer->phone,
                    $customer->created_at->format('d/m/Y H:i:s'),
                    $customer->updated_at->format('d/m/Y H:i:s'),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, Response::HTTP_OK, $headers);
    }
}
