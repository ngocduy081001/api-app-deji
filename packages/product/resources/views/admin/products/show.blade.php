<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">SKU: {{ $product->sku }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.products.edit', $product) }}">
                    <x-admin.button>Edit Product</x-admin.button>
                </a>
                <a href="{{ route('admin.products.index') }}">
                    <x-admin.button variant="secondary">Back</x-admin.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Details -->
            <x-admin.card title="Product Details">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">SKU</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->sku }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->category?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <x-admin.badge :color="$product->is_active ? 'green' : 'gray'">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </x-admin.badge>
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->description ?: '-' }}</dd>
                    </div>
                </dl>
            </x-admin.card>

            <!-- Pricing & Stock -->
            <x-admin.card title="Pricing & Inventory">
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Regular Price</dt>
                        <dd class="mt-1 text-lg font-bold text-gray-900">{{ format_price($product->price) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sale Price</dt>
                        <dd class="mt-1 text-lg font-bold text-gray-900">
                            {{ $product->sale_price ? format_price($product->sale_price) : '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Stock Quantity</dt>
                        <dd class="mt-1">
                            <x-admin.badge :color="$product->stock_quantity > 10
                                ? 'green'
                                : ($product->stock_quantity > 0
                                    ? 'yellow'
                                    : 'red')">
                                {{ $product->stock_quantity }} units
                            </x-admin.badge>
                        </dd>
                    </div>
                </dl>
            </x-admin.card>

            <!-- Variants -->
            @if ($product->variants->isNotEmpty())
                <x-admin.card title="Product Variants">
                    <div class="space-y-2">
                        @foreach ($product->variants as $variant)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $variant->name }}</div>
                                    <div class="text-xs text-gray-500">SKU: {{ $variant->sku }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ format_price($variant->price) }}</div>
                                    <div class="text-xs text-gray-500">Stock: {{ $variant->stock_quantity }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-admin.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Stats -->
            <x-admin.card title="Statistics">
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Views</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ number_format($product->view_count) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Featured</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $product->is_featured ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Created</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $product->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Updated</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $product->updated_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </x-admin.card>

            <!-- Image -->
            @if ($product->featured_image)
                <x-admin.card title="Featured Image">
                    <img src="{{ $product->featured_image }}" alt="{{ $product->name }}" class="w-full rounded-md">
                </x-admin.card>
            @endif

            <!-- Actions -->
            <x-admin.card title="Actions">
                <div class="space-y-2">
                    <a href="{{ route('products.show', $product->slug) }}" target="_blank"
                        class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        View on Site
                    </a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                        onsubmit="return confirm('Are you sure you want to delete this product?')">
                        @csrf
                        @method('DELETE')
                        <x-admin.button type="submit" variant="danger" class="w-full">
                            Delete Product
                        </x-admin.button>
                    </form>
                </div>
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
