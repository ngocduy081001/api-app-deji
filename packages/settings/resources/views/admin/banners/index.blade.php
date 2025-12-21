<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý Banner</h1>
                <p class="mt-1 text-sm text-gray-600">Quản lý banner hệ thống</p>
            </div>
            <a href="{{ route('admin.banners.create') }}">
                <x-admin.button>Tạo banner mới</x-admin.button>
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí</label>
                <select name="position"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    @foreach ($positions as $pos)
                        <option value="{{ $pos }}" {{ $position === $pos ? 'selected' : '' }}>
                            {{ ucfirst($pos) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <x-admin.button type="submit">Lọc</x-admin.button>
        </form>
    </x-admin.card>

    <!-- Banners List -->
    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu
                            đề</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hình
                            ảnh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vị
                            trí</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thứ
                            tự</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng
                            thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao
                            tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($banners as $banner)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $banner->title }}</div>
                                @if ($banner->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($banner->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($banner->image)
                                    <img src="{{ $banner->image }}" alt="{{ $banner->title }}"
                                        class="h-16 w-24 object-cover rounded">
                                @else
                                    <span class="text-gray-400">Không có</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ucfirst($banner->position) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $banner->order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $banner->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $banner->is_active ? 'Hoạt động' : 'Tắt' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <x-admin.action-buttons :model="$banner" routePrefix="admin.banners" :showView="false"
                                    deleteMessage="Bạn có chắc chắn muốn xóa banner này?" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Chưa có banner nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($banners->hasPages())
            <div class="mt-4">
                {{ $banners->links() }}
            </div>
        @endif
    </x-admin.card>
</x-admin-layout>
