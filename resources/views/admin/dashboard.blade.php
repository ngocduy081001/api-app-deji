<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('common.dashboard') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('common.welcome') }}, {{ auth()->user()->name }}!</p>
        </div>
    </x-slot>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Products -->
        <x-admin.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <x-admin.icon name="products" class="h-6 w-6 text-blue-600" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                </div>
            </div>
        </x-admin.card>

        <!-- Total Orders -->
        <x-admin.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <x-admin.icon name="orders" class="h-6 w-6 text-green-600" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                </div>
            </div>
        </x-admin.card>

        <!-- Total Articles -->
        <x-admin.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <x-admin.icon name="news" class="h-6 w-6 text-purple-600" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Articles</p>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                </div>
            </div>
        </x-admin.card>

        <!-- Total Users -->
        <x-admin.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <x-admin.icon name="users" class="h-6 w-6 text-yellow-600" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                </div>
            </div>
        </x-admin.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <x-admin.card title="Recent Orders">
            <div class="text-center py-12 text-gray-500">
                <p class="text-sm">No orders yet</p>
            </div>
        </x-admin.card>

        <!-- Quick Actions -->
        <x-admin.card title="Quick Actions">
            <div class="space-y-2">
                <a href="{{ route('admin.products.create') }}"
                    class="block w-full px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-md text-left transition-colors">
                    <div class="flex items-center">
                        <x-admin.icon name="products" class="h-5 w-5 text-gray-600 mr-3" />
                        <span class="text-sm font-medium text-gray-900">Add New Product</span>
                    </div>
                </a>

                <a href="{{ route('admin.articles.create') }}"
                    class="block w-full px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-md text-left transition-colors">
                    <div class="flex items-center">
                        <x-admin.icon name="news" class="h-5 w-5 text-gray-600 mr-3" />
                        <span class="text-sm font-medium text-gray-900">Write Article</span>
                    </div>
                </a>


            </div>
        </x-admin.card>
    </div>
</x-admin-layout>
