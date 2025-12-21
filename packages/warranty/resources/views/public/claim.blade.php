<x-guest-layout>
    <x-slot name="title">Kích hoạt bảo hành</x-slot>

    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Kích hoạt bảo hành sản phẩm</h2>
            <p class="mt-2 text-sm text-gray-600">
                Quét QR và hoàn tất biểu mẫu để kích hoạt bảo hành chính hãng cho sản phẩm của bạn.
            </p>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
            <div class="flex flex-col space-y-2 text-sm text-gray-700">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-900">Sản phẩm:</span>
                    <span>{{ $warranty->product->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-900">Mã bảo hành:</span>
                    <span>{{ $warranty->warranty_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-900">Trạng thái hiện tại:</span>
                    <span class="capitalize">
                        @switch($warranty->status)
                            @case('active')
                                Đang hoạt động
                            @break

                            @case('expired')
                                Hết hạn
                            @break

                            @default
                                Chưa kích hoạt
                        @endswitch
                    </span>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-md bg-red-50 border border-red-200 text-sm text-red-800">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($warranty->status === 'expired')
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md text-sm text-yellow-800">
                Mã bảo hành này đã hết hạn. Vui lòng liên hệ bộ phận chăm sóc khách hàng để được hỗ trợ.
            </div>
        @elseif($warranty->status === 'active')
            <div class="p-4 bg-green-50 border border-green-200 rounded-md text-sm text-green-800">
                Mã bảo hành đã được kích hoạt trước đó. Nếu cần cập nhật thông tin, vui lòng liên hệ tổng đài CSKH.
            </div>
        @else
            <form method="POST" action="{{ route('warranty.claim.store', ['code' => $warranty->warranty_code]) }}"
                class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('customer_name') border-red-300 @enderror"
                        required placeholder="Nguyễn Văn A">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('customer_email') border-red-300 @enderror"
                        placeholder="ban@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('customer_phone') border-red-300 @enderror"
                        required placeholder="0912 345 678">
                </div>

                <x-admin.button type="submit" class="w-full justify-center">
                    Kích hoạt bảo hành
                </x-admin.button>
            </form>
        @endif
    </div>
</x-guest-layout>
