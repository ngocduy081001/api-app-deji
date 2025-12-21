<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bảo hành</h1>
                <p class="mt-1 text-sm text-gray-600">Quản lý bảo hành theo sản phẩm</p>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Tên sản phẩm hoặc SKU..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                <select name="category_id"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <x-admin.button type="submit" class="px-3" title="Lọc">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </x-admin.button>
                <a href="{{ route('admin.warranties.index') }}">
                    <x-admin.button variant="secondary" class="px-3" title="Đặt lại">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </x-admin.button>
                </a>
            </div>
        </form>
    </x-admin.card>

    <!-- Products Table -->
    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sản phẩm</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Danh mục</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.products.show', $product) }}" class="flex items-center">
                                    <div class="h-12 w-12 flex-shrink-0 rounded-md bg-gray-200 overflow-hidden">
                                        @if ($product->featured_image)
                                            <img src="{{ $product->featured_image }}" alt="{{ $product->name }}"
                                                class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    </div>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $product->sku ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">

                                @foreach ($product->categories as $category)
                                    <span class="text-sm text-gray-900">{{ $category->name }}</span>
                                @endforeach
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <a title="Xem bảo hành" href="{{ route('admin.warranties.show', $product) }}"
                class="inline-flex items-center p-2 bg-black text-white text-sm font-medium rounded-md hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>
        </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                <div class="text-sm">Không tìm thấy sản phẩm nào.</div>
            </td>
        </tr>
        @endforelse
        </tbody>
        </table>
        </div>

        @if ($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </x-admin.card>
</x-admin-layout>
