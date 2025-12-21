<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tạo bảo hành mới</h1>
                <p class="mt-1 text-sm text-gray-600">Thêm bảo hành mới vào hệ thống</p>
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

    <form method="POST" action="{{ route('admin.warranties.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <x-admin.card title="Thông tin cơ bản">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mã bảo hành *</label>
                            <input type="text" name="warranty_code" id="warranty_code"
                                value="{{ old('warranty_code') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="Nhập mã bảo hành">
                            @error('warranty_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sản phẩm *</label>
                            <select name="product_id" id="product_id" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                                <option value="">Chọn sản phẩm</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} @if ($product->sku)
                                            ({{ $product->sku }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Khách hàng</label>
                            <select name="customer_id" id="customer_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                                <option value="">Chọn khách hàng (tùy chọn)</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} @if ($customer->email)
                                            ({{ $customer->email }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Warranty Details -->
                <x-admin.card title="Chi tiết bảo hành">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thời hạn bảo hành
                                (tháng)</label>
                            <input type="number" name="month" id="month" value="{{ old('month', 12) }}"
                                min="1" max="120"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="12">
                            <p class="mt-1 text-xs text-gray-500">Mặc định: 12 tháng</p>
                            @error('month')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kích hoạt</label>
                            <input type="datetime-local" name="active_date" id="active_date"
                                value="{{ old('active_date') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                            <p class="mt-1 text-xs text-gray-500">Để trống nếu chưa kích hoạt</p>
                            @error('active_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn</label>
                            <input type="datetime-local" name="time_expired" id="time_expired"
                                value="{{ old('time_expired') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                            <p class="mt-1 text-xs text-gray-500">Sẽ tự động tính nếu có ngày kích hoạt và thời hạn</p>
                            @error('time_expired')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status -->
                <x-admin.card title="Trạng thái">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select name="status" id="status"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                                <option value="clear" {{ old('status', 'clear') === 'clear' ? 'selected' : '' }}>
                                    Chưa kích hoạt
                                </option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Đang hoạt
                                    động
                                </option>
                                <option value="expired" {{ old('status') === 'expired' ? 'selected' : '' }}>Hết hạn
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <x-admin.button type="submit" class="w-full">
                                Tạo bảo hành
                            </x-admin.button>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>
    </form>
</x-admin-layout>
