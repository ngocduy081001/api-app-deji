<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tạo banner mới</h1>
            <p class="mt-1 text-sm text-gray-600">Thêm banner mới vào hệ thống</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.banners.store') }}">
        @csrf

        <x-admin.card>
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh</label>
                    <input type="text" name="image" id="image" value="{{ old('image') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                        placeholder="URL hình ảnh">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="url" class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                        <input type="text" name="url" id="url" value="{{ old('url') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    </div>

                    <div>
                        <label for="route" class="block text-sm font-medium text-gray-700 mb-2">Route</label>
                        <input type="text" name="route" id="route" value="{{ old('route') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Vị trí *</label>
                        <select name="position" id="position" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                            <option value="top" {{ old('position') == 'top' ? 'selected' : '' }}>Top</option>
                            <option value="bottom" {{ old('position') == 'bottom' ? 'selected' : '' }}>Bottom</option>
                            <option value="sidebar" {{ old('position') == 'sidebar' ? 'selected' : '' }}>Sidebar
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Thứ tự</label>
                        <input type="number" name="order" id="order" value="{{ old('order', 0) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Ngày bắt
                            đầu</label>
                        <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Ngày kết thúc</label>
                        <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                    </div>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-black focus:ring-black">
                        <span class="ml-2 text-sm text-gray-700">Kích hoạt</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.banners.index') }}">
                    <x-admin.button variant="secondary">Hủy</x-admin.button>
                </a>
                <x-admin.button type="submit">Tạo banner</x-admin.button>
            </div>
        </x-admin.card>
    </form>
</x-admin-layout>
