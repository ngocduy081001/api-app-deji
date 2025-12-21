<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Đơn hàng #{{ $order->order_number }}</h1>
                <p class="mt-1 text-sm text-gray-600">Chi tiết đơn hàng</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.orders.index') }}">
                    <x-admin.button variant="secondary">
                        <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Quay lại
                    </x-admin.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Information -->
            <x-admin.card title="Thông tin đơn hàng">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Mã đơn hàng</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $order->order_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                        <dd class="mt-1">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                @if ($order->status === 'pending')
                                    Chờ xử lý
                                @elseif($order->status === 'processing')
                                    Đang xử lý
                                @elseif($order->status === 'shipped')
                                    Đã giao hàng
                                @elseif($order->status === 'delivered')
                                    Đã nhận
                                @elseif($order->status === 'cancelled')
                                    Đã hủy
                                @endif
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phương thức thanh toán</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->payment_method ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Trạng thái thanh toán</dt>
                        <dd class="mt-1">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                @if ($order->payment_status === 'pending')
                                    Chờ thanh toán
                                @elseif($order->payment_status === 'paid')
                                    Đã thanh toán
                                @elseif($order->payment_status === 'failed')
                                    Thất bại
                                @elseif($order->payment_status === 'refunded')
                                    Đã hoàn tiền
                                @else
                                    N/A
                                @endif
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Ngày tạo</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Cập nhật lần cuối</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $order->updated_at ? $order->updated_at->format('d/m/Y H:i') : 'N/A' }}</dd>
                    </div>
                </dl>
            </x-admin.card>

            <!-- Customer Information -->
            <x-admin.card title="Thông tin khách hàng">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tên khách hàng</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $order->customer_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->customer_email ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Số điện thoại</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->customer_phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Địa chỉ</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $order->customer_address ?? ($order->address ? ($order->address . ', ' . ($order->district ?? '') . ', ' . ($order->city ?? '') . ', ' . ($order->province ?? '')) : 'N/A') }}
                        </dd>
                    </div>
                </dl>
            </x-admin.card>

            <!-- Order Items -->
            @if ($order->orderItems && $order->orderItems->count() > 0)
                <x-admin.card title="Sản phẩm trong đơn hàng">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sản phẩm</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Số lượng</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Đơn giá</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($order->orderItems as $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->product->name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ number_format($item->price, 0, ',', '.') }} đ
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ number_format($item->total, 0, ',', '.') }} đ
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-admin.card>
            @endif

            <!-- Notes -->
            @if ($order->notes)
                <x-admin.card title="Ghi chú">
                    <p class="text-sm text-gray-900 whitespace-pre-line">{{ $order->notes }}</p>
                </x-admin.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <x-admin.card title="Tổng kết đơn hàng">
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Tạm tính</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            {{ number_format($order->subtotal, 0, ',', '.') }} đ
                        </dd>
                    </div>
                    @if ($order->tax > 0)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Thuế</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ number_format($order->tax, 0, ',', '.') }} đ
                            </dd>
                        </div>
                    @endif
                    @if ($order->shipping_fee > 0)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Phí vận chuyển</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ number_format($order->shipping_fee, 0, ',', '.') }} đ
                            </dd>
                        </div>
                    @endif
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <dt class="text-base font-semibold text-gray-900">Tổng cộng</dt>
                            <dd class="text-base font-bold text-gray-900">
                                {{ number_format($order->total, 0, ',', '.') }} đ
                            </dd>
                        </div>
                    </div>
                </dl>
            </x-admin.card>

            <!-- Appointment Information -->
            @if ($order->appointment_date)
                <x-admin.card title="Thông tin lịch hẹn">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ngày hẹn</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $order->appointment_date->format('d/m/Y') }}
                            </dd>
                        </div>
                        @if ($order->appointment_time)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Giờ hẹn</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ is_string($order->appointment_time) ? $order->appointment_time : $order->appointment_time->format('H:i') }}
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                            <dd class="mt-1">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->appointment_status === 'confirmed' ? 'bg-blue-100 text-blue-800' : ($order->appointment_status === 'completed' ? 'bg-green-100 text-green-800' : ($order->appointment_status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                    @if ($order->appointment_status === 'pending')
                                        Chờ xác nhận
                                    @elseif($order->appointment_status === 'confirmed')
                                        Đã xác nhận
                                    @elseif($order->appointment_status === 'completed')
                                        Hoàn thành
                                    @elseif($order->appointment_status === 'cancelled')
                                        Đã hủy
                                    @endif
                                </span>
                            </dd>
                        </div>
                        @if ($order->appointment_note)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ghi chú lịch hẹn</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                                    {{ $order->appointment_note }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </x-admin.card>
            @endif
        </div>
    </div>
</x-admin-layout>

