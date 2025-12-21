<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Đơn hàng</h1>
                <p class="mt-1 text-sm text-gray-600">Quản lý đơn hàng và lịch hẹn</p>
            </div>
            <a href="{{ route('admin.orders.create') }}">
                <x-admin.button>
                    <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tạo đơn hàng
                </x-admin.button>
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Mã đơn, tên, email, SĐT..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="status"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý
                    </option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Đã giao hàng</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã nhận</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái lịch hẹn</label>
                <select name="appointment_status"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('appointment_status') == 'pending' ? 'selected' : '' }}>Chờ xác
                        nhận</option>
                    <option value="confirmed" {{ request('appointment_status') == 'confirmed' ? 'selected' : '' }}>Đã
                        xác nhận</option>
                    <option value="completed" {{ request('appointment_status') == 'completed' ? 'selected' : '' }}>Hoàn
                        thành</option>
                    <option value="cancelled" {{ request('appointment_status') == 'cancelled' ? 'selected' : '' }}>Đã
                        hủy</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hẹn</label>
                <input type="date" name="appointment_date" value="{{ request('appointment_date') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
            </div>
            <div class="flex gap-2">
                <x-admin.button type="submit" class="px-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Tìm kiếm
                </x-admin.button>
                <a href="{{ route('admin.orders.index') }}">
                    <x-admin.button variant="secondary" class="px-4">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Đặt lại
                    </x-admin.button>
                </a>
            </div>
        </form>
    </x-admin.card>

    <!-- Orders Table -->
    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mã đơn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Khách hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tổng tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lịch hẹn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ngày tạo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $order->customer_name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->customer_phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ number_format($order->total, 0, ',', '.') }} đ
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4">
                                @if ($order->appointment_date)
                                    <div class="text-sm text-gray-900">
                                        <div>{{ $order->appointment_date->format('d/m/Y') }}</div>
                                        @if ($order->appointment_time)
                                            <div class="text-xs text-gray-500">{{ $order->appointment_time }}</div>
                                        @endif
                                    </div>
                                    <span
                                        class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->appointment_status === 'confirmed' ? 'bg-blue-100 text-blue-800' : ($order->appointment_status === 'completed' ? 'bg-green-100 text-green-800' : ($order->appointment_status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
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
                                @else
                                    <span class="text-sm text-gray-400">Không có</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <x-admin.action-buttons :model="$order" routePrefix="admin.orders" :showEdit="false"
                                    :showDelete="false" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-sm">Không tìm thấy đơn hàng nào.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </x-admin.card>
</x-admin-layout>
