<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tạo người dùng mới</h1>
                <p class="mt-1 text-sm text-gray-600">Thêm người dùng mới vào hệ thống</p>
            </div>
            <a href="{{ route('admin.users.index') }}">
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

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <x-admin.card title="Thông tin người dùng">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="Nhập tên người dùng">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="Nhập email">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu *</label>
                            <input type="password" name="password" id="password" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="Nhập mật khẩu">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="Nhập lại mật khẩu">
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <x-admin.card title="Thao tác">
                    <div class="pt-4">
                        <x-admin.button type="submit" class="w-full">
                            Tạo người dùng
                        </x-admin.button>
                    </div>
                </x-admin.card>
            </div>
        </div>
    </form>
</x-admin-layout>
