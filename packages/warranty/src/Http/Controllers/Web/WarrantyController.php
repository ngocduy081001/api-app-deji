<?php

namespace Vendor\Warranty\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Vendor\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Vendor\Product\Models\Product;
use Vendor\Product\Models\ProductCategory;
use Vendor\Warranty\Http\Requests\GenerateWarrantyQrRequest;
use Vendor\Warranty\Http\Requests\StoreWarrantyRequest;
use Vendor\Warranty\Http\Requests\UpdateWarrantyRequest;
use Vendor\Warranty\Models\Warranty;

class WarrantyController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with('categories');

        // Search by product name or SKU
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by category if needed
        if ($request->has('category_id') && $request->input('category_id') !== '') {
            $query->where('category_id', $request->input('category_id'));
        }



        $products = $query->orderBy('name')->paginate(20);

        $categories = ProductCategory::orderBy('name')->get();

        return view('warranty::admin.index', compact('products', 'categories'));
    }

    /**
     * Display warranties for a specific product.
     */
    public function show(Product $product)
    {
        $product->load('category');
        $warranties = Warranty::where('product_id', $product->id)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->get();
        $customers = Customer::orderBy('name')->get();

        return view('warranty::admin.show', compact('product', 'warranties', 'customers'));
    }

    /**
     * Show printable view for unprinted warranties.
     */
    public function bulkPrint(Product $product)
    {
        $product->load('category');

        $warranties = Warranty::where('product_id', $product->id)
            ->whereNotNull('qr_path')
            ->whereNull('printed_at')
            ->orderBy('created_at')
            ->get();

        if ($warranties->isEmpty()) {
            return redirect()
                ->route('admin.warranties.show', $product)
                ->with('error', 'Tất cả mã QR đã được in hoặc chưa có mã nào để in.');
        }

        return view('warranty::admin.print', compact('product', 'warranties'));
    }

    /**
     * Mark bulk printed warranties as printed.
     */
    public function markBulkPrinted(Request $request, Product $product)
    {
        $ids = $request->input('warranty_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Không có mã nào được chọn để đánh dấu đã in.');
        }

        Warranty::where('product_id', $product->id)
            ->whereIn('id', $ids)
            ->update(['printed_at' => now()]);

        return redirect()
            ->route('admin.warranties.show', $product)
            ->with('success', 'Đã đánh dấu in cho ' . count($ids) . ' mã QR.');
    }

    /**
     * Plain preview (QR only) for printing via window.open.
     */
    public function printPreview(Product $product)
    {
        $warranties = Warranty::where('product_id', $product->id)
            ->whereNotNull('qr_path')
            ->whereNull('printed_at')
            ->orderBy('created_at')
            ->get();

        if ($warranties->isEmpty()) {
            abort(404);
        }

        return view('warranty::admin.list-qr', compact('warranties'));
    }

    /**
     * Store a newly created warranty for a product.
     */
    public function store(StoreWarrantyRequest $request, Product $product)
    {
        $data = $request->validated();

        // Set product_id from route
        $data['product_id'] = $product->id;

        // Set default values
        $data['status'] = $data['status'] ?? 'clear';
        $data['month'] = $data['month'] ?? config('warranty.default_months', 12);

        // Calculate expiration date if active_date and month are provided
        if (!empty($data['active_date']) && !empty($data['month'])) {
            $activeDate = new \DateTime($data['active_date']);
            $activeDate->modify("+{$data['month']} months");
            $data['time_expired'] = $activeDate->format('Y-m-d H:i:s');
        }

        $warranty = Warranty::create($data);
        $this->regenerateQr($warranty);

        return redirect()
            ->route('admin.warranties.show', $product)
            ->with('success', 'Bảo hành đã được tạo thành công.');
    }

    /**
     * Show the form for editing the specified warranty.
     */
    public function edit(Warranty $warranty)
    {


        return view('warranty::admin.edit', compact('warranty'));
    }

    /**
     * Update the specified warranty.
     */
    public function update(UpdateWarrantyRequest $request, Warranty $warranty)
    {
        $data = $request->validated();

        // Calculate expiration date if active_date and month are provided
        if (!empty($data['active_date']) && !empty($data['month'])) {
            $activeDate = new \DateTime($data['active_date']);
            $activeDate->modify("+{$data['month']} months");
            $data['time_expired'] = $activeDate->format('Y-m-d H:i:s');
        } elseif (!empty($data['active_date']) && $warranty->month) {
            // Use existing month if not provided
            $activeDate = new \DateTime($data['active_date']);
            $activeDate->modify("+{$warranty->month} months");
            $data['time_expired'] = $activeDate->format('Y-m-d H:i:s');
        } elseif (!empty($data['month']) && $warranty->active_date) {
            // Use existing active_date if not provided
            $activeDate = new \DateTime($warranty->active_date);
            $activeDate->modify("+{$data['month']} months");
            $data['time_expired'] = $activeDate->format('Y-m-d H:i:s');
        }

        $originalCode = $warranty->warranty_code;
        $warranty->update($data);

        if ($originalCode !== $warranty->warranty_code || empty($warranty->qr_path)) {
            $this->regenerateQr($warranty);
        }

        // Update customer information if provided
        if ($request->has('customer_id') && $request->filled('customer_id')) {
            $customer = Customer::find($request->input('customer_id'));
            if ($customer) {
                $customerData = [];
                if ($request->has('customer_name')) {
                    $customerData['name'] = $request->input('customer_name');
                }
                if ($request->has('customer_email')) {
                    $customerData['email'] = $request->input('customer_email');
                }
                if ($request->has('customer_phone')) {
                    $customerData['phone'] = $request->input('customer_phone');
                }
                if (!empty($customerData)) {
                    $customer->update($customerData);
                }
            }
        }

        return redirect()
            ->route('admin.warranties.show', $warranty->product)
            ->with('success', 'Bảo hành đã được cập nhật thành công.');
    }

    /**
     * Remove the specified warranty.
     */
    public function destroy(Warranty $warranty)
    {
        $product = $warranty->product;
        $this->deleteQrImage($warranty->qr_path);
        $warranty->delete();

        return redirect()
            ->route('admin.warranties.show', $product)
            ->with('success', 'Bảo hành đã được xóa thành công.');
    }

    /**
     * Generate multiple warranties + QR codes for a product.
     */
    public function generateQrBatch(GenerateWarrantyQrRequest $request, Product $product)
    {
        $quantity = (int) $request->input('quantity');
        $month = $request->input('month', config('warranty.default_months', 12));
        $prefix = $request->input('code_prefix');
        $created = 0;

        DB::beginTransaction();

        try {
            for ($i = 0; $i < $quantity; $i++) {
                $code = $this->buildUniqueCode($product, $prefix);

                $warranty = Warranty::create([
                    'product_id' => $product->id,
                    'warranty_code' => $code,
                    'status' => 'clear',
                    'month' => $month,
                ]);

                $this->regenerateQr($warranty);
                $created++;
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            report($th);

            return back()->with('error', 'Không thể tạo QR. Vui lòng thử lại.');
        }

        return redirect()
            ->route('admin.warranties.show', $product)
            ->with('success', "Đã tạo {$created} mã QR mới.");
    }

    /**
     * Download QR image for printing.
     */
    public function downloadQr(Warranty $warranty)
    {
        $disk = config('warranty.qr.disk', 'public');

        if (!$warranty->qr_path || !Storage::disk($disk)->exists($warranty->qr_path)) {
            return back()->with('error', 'Không tìm thấy file QR.');
        }

        if ($warranty->printed_at) {
            return back()->with('error', 'Mã QR này đã được in trước đó, không thể in lại.');
        }

        $extension = pathinfo($warranty->qr_path, PATHINFO_EXTENSION) ?: 'png';
        $fileName = $warranty->warranty_code . '.' . $extension;
        $filePath = Storage::disk($disk)->path($warranty->qr_path);

        $warranty->forceFill([
            'printed_at' => now(),
        ])->save();

        return response()->download($filePath, $fileName);
    }

    protected function regenerateQr(Warranty $warranty): void
    {
        if (!$warranty->warranty_code) {
            return;
        }

        $this->deleteQrImage($warranty->qr_path);

        $path = $this->generateQrImage($warranty);

        $warranty->forceFill(['qr_path' => $path])->save();
    }

    protected function generateQrImage(Warranty $warranty): string
    {
        $disk = config('warranty.qr.disk', 'public');
        $directory = trim(config('warranty.qr.path', 'warranty-qrs'), '/');
        $size = config('warranty.qr.size', 600);
        $margin = config('warranty.qr.margin', 2);

        // Ensure directory exists
        if (!Storage::disk($disk)->exists($directory)) {
            Storage::disk($disk)->makeDirectory($directory);
        }

        $usePng = extension_loaded('imagick');
        $format = $usePng ? 'png' : 'svg';
        $extension = $format;

        $fileName = $directory . '/' . Str::slug($warranty->warranty_code) . '-' . Str::lower(Str::ulid()) . '.' . $extension;
        $claimUrl = $warranty->claim_url ?? route('warranty.claim.show', ['code' => $warranty->warranty_code], absolute: true);

        $qrImage = QrCode::format($format)
            ->size($size)
            ->margin($margin)
            ->generate($claimUrl);

        Storage::disk($disk)->put($fileName, $qrImage);

        return $fileName;
    }

    protected function deleteQrImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $disk = config('warranty.qr.disk', 'public');

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    protected function buildUniqueCode(Product $product, ?string $prefix = null): string
    {
        $base = $prefix ?: ($product->sku ?: 'PRD' . $product->id);
        $base = Str::upper(Str::slug($base, ''));
        $base = $base ?: 'WRN';

        do {
            $random = Str::upper(Str::random(6));
            $code = "{$base}-{$random}";
        } while (Warranty::where('warranty_code', $code)->exists());

        return $code;
    }
}
