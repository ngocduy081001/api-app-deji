<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Chỉnh sửa bảo hành</h1>
                <p class="mt-1 text-sm text-gray-600">Cập nhật thông tin bảo hành</p>
            </div>
            <a href="{{ route('admin.warranties.index') }}">
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

    @livewire('warranty::edit-warranty', ['warranty' => $warranty])
</x-admin-layout>
