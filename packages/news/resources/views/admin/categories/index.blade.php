<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Chuyên mục</h1>
                <p class="mt-1 text-sm text-gray-600">Quản lý chuyên mục tin tức</p>
            </div>
            <a href="{{ route('admin.news-categories.create') }}">
                <x-admin.button>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Chuyên mục mới
                </x-admin.button>
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên chuyên mục..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
            </div>

            <div class="flex gap-2">
                <x-admin.button type="submit">Lọc</x-admin.button>
                <a href="{{ route('admin.news-categories.index') }}">
                    <x-admin.button variant="secondary">Đặt lại</x-admin.button>
                </a>
            </div>
        </form>
    </x-admin.card>

    <!-- Categories Table -->
    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Chuyên mục</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Chuyên mục cha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sắp xếp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Số bài viết</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ngày tạo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if ($category->image)
                                        <div class="h-12 w-16 flex-shrink-0 rounded-md bg-gray-200 overflow-hidden">
                                            <img src="{{ $category->image }}" alt="{{ $category->name }}"
                                                class="h-full w-full object-cover">
                                        </div>
                                    @endif
                                    <div class="{{ $category->image ? 'ml-4' : '' }}">
                                        <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                        @if ($category->description)
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($category->description, 60) }}
                                            </div>
                                        @endif
                                        @if ($category->slug)
                                            <div class="text-xs text-gray-400">{{ $category->slug }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $category->parent?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-admin.badge :color="$category->is_active ? 'green' : 'gray'">
                                    {{ $category->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                </x-admin.badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $category->sort_order }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $category->articles_count ?? $category->articles()->count() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $category->created_at?->format('d/m/Y') ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <x-admin.action-buttons :model="$category" routePrefix="admin.news-categories"
                                    :showView="true" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-sm">Không tìm thấy chuyên mục nào.</div>
                                <a href="{{ route('admin.news-categories.create') }}"
                                    class="mt-2 text-black hover:text-gray-700 text-sm font-medium">
                                    Tạo chuyên mục đầu tiên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $categories->links() }}
            </div>
        @endif
    </x-admin.card>
</x-admin-layout>
