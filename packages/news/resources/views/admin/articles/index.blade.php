<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bài viết</h1>
                <p class="mt-1 text-sm text-gray-600">Quản lý bài viết và tin tức</p>
            </div>
            <a href="{{ route('admin.articles.create') }}">
                <x-admin.button>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Bài viết mới
                </x-admin.button>
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiêu đề bài viết..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Chuyên mục</label>
                <select name="category_id"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    <option value="">Tất cả chuyên mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="status"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    <option value="">Tất cả trạng thái</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản
                    </option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Đã lưu trữ
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <x-admin.button type="submit" class="flex-1">Lọc</x-admin.button>
                <a href="{{ route('admin.articles.index') }}">
                    <x-admin.button variant="secondary">Đặt lại</x-admin.button>
                </a>
            </div>
        </form>
    </x-admin.card>

    <!-- Articles Table -->
    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bài viết</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tác giả</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Chuyên mục</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lượt
                            xem
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($articles as $article)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if ($article->featured_image)
                                        <div class="h-12 w-16 flex-shrink-0 rounded-md bg-gray-200 overflow-hidden">
                                            <img src="{{ $article->featured_image }}" alt="{{ $article->title }}"
                                                class="h-full w-full object-cover">
                                        </div>
                                    @endif
                                    <div class="{{ $article->featured_image ? 'ml-4' : '' }}">
                                        <div class="text-sm font-medium text-gray-900">{{ $article->title }}</div>
                                        @if ($article->excerpt)
                                            <div class="text-sm text-gray-500">{{ Str::limit($article->excerpt, 60) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $article->author?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $article->category?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'draft' => 'gray',
                                        'published' => 'green',
                                        'archived' => 'yellow',
                                    ];
                                @endphp
                                @php
                                    $statusLabels = [
                                        'draft' => 'Bản nháp',
                                        'published' => 'Đã xuất bản',
                                        'archived' => 'Đã lưu trữ',
                                    ];
                                @endphp
                                <x-admin.badge :color="$statusColors[$article->status] ?? 'gray'">
                                    {{ $statusLabels[$article->status] ?? ucfirst($article->status) }}
                                </x-admin.badge>
                                @if ($article->is_featured)
                                    <x-admin.badge color="purple">Nổi bật</x-admin.badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($article->view_count) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $article->published_at?->format('M d, Y') ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <x-admin.action-buttons :model="$article" routePrefix="admin.articles"
                                    :showView="false" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-sm">Không tìm thấy bài viết nào.</div>
                                <a href="{{ route('admin.articles.create') }}"
                                    class="mt-2 text-black hover:text-gray-700 text-sm font-medium">
                                    Viết bài viết đầu tiên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($articles->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $articles->links() }}
            </div>
        @endif
    </x-admin.card>
</x-admin-layout>
