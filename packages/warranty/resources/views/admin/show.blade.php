<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">Quản lý bảo hành cho sản phẩm</p>
            </div>
            <a href="{{ route('admin.warranties.index') }}">
                <x-admin.button variant="secondary">
                    <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Quay lại
                </x-admin.button>
            </a>
        </div>
    </x-slot>
    @php
        $unprintedCount = $warranties->whereNull('printed_at')->whereNotNull('qr_path')->count();
    @endphp

    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Product Info -->
        <x-admin.card title="Thông tin sản phẩm">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tên sản phẩm</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $product->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">SKU</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->sku ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Danh mục</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @foreach ($product->categories as $category)
                            <span class="text-sm text-gray-900">{{ $category->name }}</span>
                        @endforeach
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Giá</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900">
                        {{ format_price($product->price) }}</dd>
                </div>
            </dl>
        </x-admin.card>

        <!-- Bulk Print CTA -->
        @if ($unprintedCount > 0)
            <div
                class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-4 border border-dashed border-black rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-900">Có {{ $unprintedCount }} mã QR chưa in</p>
                    <p class="text-xs text-gray-500">Bạn có thể in tất cả các mã này trong một trang duy nhất.</p>
                </div>
                <a href="{{ route('admin.warranties.print', $product) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-black rounded-md hover:bg-gray-800">
                    In hàng loạt
                </a>
            </div>
        @endif

        <!-- QR Batch Generator -->
        <x-admin.card title="Tạo QR hàng loạt">
            <form method="POST" action="{{ route('admin.warranties.qr-batch', $product) }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng QR *</label>
                    <input type="number" name="quantity" min="1" max="200"
                        value="{{ old('quantity', 10) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('quantity') border-red-300 @enderror">
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thời hạn (tháng)</label>
                    <input type="number" name="month" min="1" max="120"
                        value="{{ old('month', config('warranty.default_months', 12)) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('month') border-red-300 @enderror">
                    @error('month')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiền tố mã (tùy chọn)</label>
                    <input type="text" name="code_prefix" value="{{ old('code_prefix', $product->sku) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('code_prefix') border-red-300 @enderror"
                        placeholder="VD: {{ $product->sku ?? 'SPX' }}">
                    @error('code_prefix')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-end">
                    <x-admin.button type="submit" class="w-full">
                        Tạo QR
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>

        <!-- Warranties List -->
        <x-admin.card title="Danh sách bảo hành">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mã bảo hành</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                QR</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Khách hàng</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thời hạn</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($warranties as $warranty)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $warranty->warranty_code }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($warranty->qr_path)
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($warranty->qr_path) }}"
                                                    alt="QR {{ $warranty->warranty_code }}"
                                                    class="h-16 w-16 border border-gray-200 rounded-md bg-white p-1">
                                                <div class="text-xs space-y-1">
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($warranty->qr_path) }}"
                                                        target="_blank"
                                                        class="text-black hover:underline font-medium">Xem
                                                        QR</a>
                                                    <div>
                                                        @if ($warranty->printed_at)
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 text-[11px] font-medium rounded-full bg-green-50 text-green-700 border border-green-100">
                                                                Đã in lúc
                                                                {{ $warranty->printed_at->format('d/m/Y H:i') }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 text-[11px] font-medium rounded-full bg-gray-50 text-gray-600 border border-gray-100">
                                                                Chưa in
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Chưa có QR</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $warranty->customer?->name ?? '-' }}</div>
                                    @if ($warranty->customer?->email)
                                        <div class="text-xs text-gray-500">{{ $warranty->customer->email }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $warranty->month }} tháng</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.warranties.edit', $warranty) }}"
                                            class="p-2 text-black hover:text-gray-700 hover:bg-gray-100 rounded-md transition-colors"
                                            title="Chỉnh sửa">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.warranties.destroy', $warranty) }}"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors"
                                                title="Xóa">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <div class="text-sm">Chưa có bảo hành nào cho sản phẩm này.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-admin.card>
    </div>
</x-admin-layout>
