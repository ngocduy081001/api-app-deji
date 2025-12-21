<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <x-admin.card title="Thông tin cơ bản">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã bảo hành *</label>
                        <div class="text-sm text-gray-900">{{ $warranty->warranty_code }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sản phẩm *</label>
                        <div class="text-sm text-gray-900">{{ $warranty->product->name }} @if ($warranty->product->sku)
                                ({{ $warranty->product->sku }})
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên khách hàng *</label>
                        <input autocomplete="off" type="text" value="{{ $customer_name }}"
                            wire:key="customer_name-{{ $warranty->id }}-{{ $resetCounter }}" wire:model="customer_name"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email khách hàng *</label>
                        <input autocomplete="off" type="email" value="{{ $customer_email }}"
                            wire:model="customer_email"
                            wire:key="customer_email-{{ $warranty->id }}-{{ $resetCounter }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại khách hàng
                            *</label>
                        <input autocomplete="off" type="text" value="{{ $customer_phone }}"
                            wire:key="customer_phone-{{ $warranty->id }}-{{ $resetCounter }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    </div>
                </div>
            </x-admin.card>

            <!-- Warranty Details -->
            <x-admin.card title="Chi tiết bảo hành">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thời hạn bảo hành
                            (tháng)</label>
                        <input autocomplete="off" type="number" wire:model="month" min="1" max="120"
                            wire:key="month-{{ $warranty->id }}-{{ $resetCounter }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('month') border-red-300 @enderror"
                            placeholder="12">
                        <p class="mt-1 text-xs text-gray-500">Mặc định: 12 tháng</p>
                        @error('month')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kích hoạt</label>
                        <input autocomplete="off" type="date" wire:model="active_date"
                            wire:key="active_date-{{ $warranty->id }}-{{ $resetCounter }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('active_date') border-red-300 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Để trống nếu chưa kích hoạt</p>
                        @error('active_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn</label>
                        <input autocomplete="off" type="date" wire:model="time_expired"
                            wire:key="time_expired-{{ $warranty->id }}-{{ $resetCounter }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('time_expired') border-red-300 @enderror">
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
                        <select autocomplete="off" wire:model="status"
                            wire:key="status-{{ $warranty->id }}-{{ $resetCounter }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('status') border-red-300 @enderror">
                            <option value="clear">Chưa kích hoạt</option>
                            <option value="active">Đang hoạt động</option>
                            <option value="expired">Hết hạn</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-200 space-y-3">
                        <x-admin.button type="button" wire:click="update" class="w-full">
                            Cập nhật bảo hành
                        </x-admin.button>
                        <button type="button" wire:click="resetForm"
                            class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Làm mới
                        </button>
                    </div>
                </div>
            </x-admin.card>
        </div>
    </div>
</div>
