<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tạo sản phẩm mới</h1>
                <p class="mt-1 text-sm text-gray-600">Thêm sản phẩm mới vào danh mục</p>
            </div>
            <a href="{{ route('admin.products.index') }}">
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



    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <x-admin.card title="Thông tin cơ bản">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm *</label>
                            <input type="text" name="name" id="product-name" value="{{ old('name') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="Nhập tên sản phẩm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đường dẫn (Slug)</label>
                            <input type="text" name="slug" id="product-slug" value="{{ old('slug') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="tu-dong-tao">
                            <p class="mt-1 text-xs text-gray-500">Tự động tạo từ tên sản phẩm</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mã SKU</label>
                            <input type="text" name="sku" value="{{ old('sku') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="Để trống để tự động tạo">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                            <textarea name="short_description" id="short-description" rows="3"
                                class="w-full rounded-md  shadow-sm focus:border-black focus:ring-black" placeholder="Mô tả ngắn gọn về sản phẩm">{{ old('short_description') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
                            <textarea name="description" id="description" rows="10"
                                class="w-full rounded-md  shadow-sm focus:border-black focus:ring-black" placeholder="Mô tả đầy đủ về sản phẩm">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Technical Specifications -->
                <x-admin.card title="Thông số kỹ thuật">
                    <div x-data="technicalSpecs()" class="space-y-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm text-gray-600">Thêm các thông số kỹ thuật của sản phẩm</p>
                            <button type="button" @click="addSpec()"
                                class="text-sm text-black hover:text-gray-700 font-medium">
                                + Thêm thông số
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(spec, index) in specs" :key="index">
                                <div class="flex gap-2 items-start">
                                    <div class="flex-1">
                                        <input type="text" :name="'specifications[' + index + '][name]'"
                                            x-model="spec.name" placeholder="Tên thông số"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black text-sm"
                                            required>
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" :name="'specifications[' + index + '][value]'"
                                            x-model="spec.value" placeholder="Giá trị"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black text-sm"
                                            required>
                                    </div>
                                    <button type="button" @click="removeSpec(index)"
                                        class="p-2 text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            <div x-show="specs.length === 0" class="text-center py-8 text-gray-400 text-sm">
                                Chưa có thông số nào. Click "+ Thêm thông số" để bắt đầu.
                            </div>
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish -->
                <x-admin.card title="Xuất bản">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-black focus:ring-black">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">Kích hoạt</label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                {{ old('is_featured') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-black focus:ring-black">
                            <label for="is_featured" class="ml-2 text-sm text-gray-700">Sản phẩm nổi bật</label>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <x-admin.button type="submit" class="w-full">
                                Tạo sản phẩm
                            </x-admin.button>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Pricing & Inventory -->
                <x-admin.card title="Giá & Kho hàng">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">₫</span>
                                    <input type="number" name="price" value="{{ old('price') }}" step="1"
                                        min="0" pattern="[0-9]*" inputmode="numeric" required
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        onpaste="this.value = this.value.replace(/[^0-9]/g, '')"
                                        class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                        placeholder="0">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá khuyến mãi</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">₫</span>
                                    <input type="number" name="sale_price" value="{{ old('sale_price') }}"
                                        step="1" min="0" pattern="[0-9]*" inputmode="numeric"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        onpaste="this.value = this.value.replace(/[^0-9]/g, '')"
                                        class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                        placeholder="0">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá thay</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">₫</span>
                                    <input type="number" name="replacement_price"
                                        value="{{ old('replacement_price') }}" step="1" min="0"
                                        pattern="[0-9]*" inputmode="numeric"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        onpaste="this.value = this.value.replace(/[^0-9]/g, '')"
                                        class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                        placeholder="0">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng trong kho
                                    *</label>
                                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                    placeholder="0">
                            </div>
                        </div>


                    </div>
                </x-admin.card>

                <!-- Category -->
                <x-admin.card title="Danh mục">
                    <div class="mb-3">
                        <input type="text" id="category-search" placeholder="Tìm kiếm danh mục..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black text-sm">
                    </div>
                    <div class="border border-gray-200 rounded-md p-3"
                        style="max-height: 384px; overflow-y: auto; overflow-x: hidden;">
                        <div class="space-y-2" id="category-list">
                            @foreach ($categories as $category)
                                <div class="flex items-center category-item"
                                    data-name="{{ strtolower($category->name) }}">
                                    <input type="checkbox" name="categories[]" id="category_{{ $category->id }}"
                                        value="{{ $category->id }}"
                                        {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-black focus:ring-black">
                                    <label for="category_{{ $category->id }}" class="ml-2 text-sm text-gray-700">
                                        {{ str_repeat('—', $category->level ?? 0) }} {{ $category->name }}
                                    </label>
                                </div>
                                @if (isset($category->children))
                                    @foreach ($category->children as $child)
                                        <div class="flex items-center ml-4 category-item"
                                            data-name="{{ strtolower($child->name) }}">
                                            <input type="checkbox" name="categories[]"
                                                id="category_{{ $child->id }}" value="{{ $child->id }}"
                                                {{ in_array($child->id, old('categories', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-black focus:ring-black">
                                            <label for="category_{{ $child->id }}"
                                                class="ml-2 text-sm text-gray-700">
                                                {{ str_repeat('—', ($category->level ?? 0) + 1) }} {{ $child->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                            @if ($categories->isEmpty())
                                <p class="text-sm text-gray-500">Chưa có danh mục nào. Vui lòng tạo danh mục trước.</p>
                            @endif
                        </div>
                        <div id="category-no-results" class="hidden text-sm text-gray-500 text-center py-4">
                            Không tìm thấy danh mục nào.
                        </div>
                    </div>
                    @error('categories')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('categories.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </x-admin.card>

                <!-- Images -->
                <x-admin.card title="Hình ảnh">
                    <div x-data="imageGallery()" class="space-y-4">
                        <!-- Featured Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh đại diện *</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div x-show="featuredImage"
                                        class="relative w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-200">
                                        <img :src="featuredImage" alt="Featured" class="w-full h-full object-cover">
                                        <button type="button" @click="removeFeatured()"
                                            class="absolute top-1 right-1 p-1 bg-red-600 text-white rounded-full hover:bg-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div x-show="!featuredImage"
                                        class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <input type="hidden" name="featured_image" id="featured-image-input"
                                        value="">
                                    <a id="lfm-featured" data-input="featured-image-input"
                                        data-preview="featured-image-preview"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Chọn ảnh
                                    </a>
                                    <div id="featured-image-preview" class="mt-2"></div>
                                    <p class="mt-1 text-xs text-gray-500">Chọn ảnh từ thư viện hoặc upload mới</p>
                                </div>
                            </div>
                        </div>

                        <!-- Gallery Images -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700">Thư viện ảnh</label>
                                <a id="lfm-gallery" data-input="gallery-images-input"
                                    data-preview="gallery-images-preview"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Thêm ảnh
                                </a>
                            </div>

                            <!-- Preview Grid -->
                            <div x-show="galleryImages.length > 0" class="grid grid-cols-3 gap-2 mb-3">
                                <template x-for="(image, index) in galleryImages" :key="image.path || index">
                                    <div class="relative group">
                                        <img :src="image.url || image"
                                            class="w-full h-24 object-cover rounded-lg border border-gray-200">
                                        <button type="button" @click="removeGalleryImage(index)"
                                            class="absolute top-1 right-1 p-1 bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <!-- Hidden inputs for gallery images -->
                            <div id="gallery-images-input"></div>
                            <div id="gallery-images-preview" class="hidden"></div>

                            <p class="text-xs text-gray-500 mt-2">Click "Thêm ảnh" để chọn nhiều ảnh từ thư viện</p>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>
    </form>

    @push('scripts')
        <!-- jQuery (required by Laravel File Manager) -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <!-- Laravel File Manager -->
        <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
        <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
        <script>
            // Function to initialize everything after CKEditor is loaded
            function initProductForm() {
                // 1. Auto-generate Slug
                const nameInput = document.getElementById('product-name');
                const slugInput = document.getElementById('product-slug');
                let manualSlug = false;

                // Check if slug was manually edited
                if (slugInput) {
                    slugInput.addEventListener('input', function() {
                        if (this.value !== '') {
                            manualSlug = true;
                        }
                    });
                }

                if (nameInput) {
                    nameInput.addEventListener('input', function() {
                        if (!manualSlug && slugInput) {
                            const slug = this.value
                                .toLowerCase()
                                .normalize('NFD')
                                .replace(/[\u0300-\u036f]/g, '') // Remove diacritics
                                .replace(/đ/g, 'd')
                                .replace(/[^\w\s-]/g, '')
                                .replace(/\s+/g, '-')
                                .replace(/-+/g, '-')
                                .replace(/^-+|-+$/g, '');
                            slugInput.value = slug;
                        }
                    });
                }

                // 2. Initialize CKEditor with Laravel File Manager (wait for CKEDITOR to be available)
                if (typeof CKEDITOR !== 'undefined') {
                    var fileManagerUrl = '{{ url('/filemanager') }}';

                    // Short Description Editor
                    CKEDITOR.replace('short-description', {
                        height: 200,
                        skin: 'kama',
                        toolbar: [
                            ['Bold', 'Italic', 'Underline', 'Strike'],
                            ['NumberedList', 'BulletedList'],
                            ['Link', 'Unlink'],
                            ['Image'],
                            ['Source']
                        ],
                        filebrowserBrowseUrl: fileManagerUrl + '?type=Files',
                        filebrowserImageBrowseUrl: fileManagerUrl + '?type=Images',
                        filebrowserUploadUrl: fileManagerUrl + '/upload?type=Files&_token=' + '{{ csrf_token() }}',
                        filebrowserImageUploadUrl: fileManagerUrl + '/upload?type=Images&_token=' +
                            '{{ csrf_token() }}',
                        contentsCss: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; color: #1f2937; }'
                    });

                    // Full Description Editor
                    CKEDITOR.replace('description', {
                        height: 600,
                        skin: 'kama',
                        toolbar: [
                            ['Source', '-', 'Save', 'NewPage', 'Preview', '-', 'Templates'],
                            ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Print', 'SpellChecker',
                                'Scayt'
                            ],
                            ['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'],
                            ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button',
                                'ImageButton', 'HiddenField'
                            ],
                            '/',
                            ['Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript'],
                            ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
                            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                            ['Link', 'Unlink', 'Anchor'],
                            ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'],
                            '/',
                            ['Styles', 'Format', 'Font', 'FontSize'],
                            ['TextColor', 'BGColor'],
                            ['Maximize', 'ShowBlocks', '-', 'About']
                        ],
                        filebrowserBrowseUrl: fileManagerUrl + '?type=Files',
                        filebrowserImageBrowseUrl: fileManagerUrl + '?type=Images',
                        filebrowserUploadUrl: fileManagerUrl + '/upload?type=Files&_token=' + '{{ csrf_token() }}',
                        filebrowserImageUploadUrl: fileManagerUrl + '/upload?type=Images&_token=' +
                            '{{ csrf_token() }}',
                        contentsCss: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; color: #1f2937; line-height: 1.6; }',
                        bodyClass: 'ckeditor-content'
                    });
                } else {
                    console.error('CKEditor is not loaded');
                }
            }

            // Wait for DOM and CKEditor to be ready
            function waitForCKEditor() {
                if (typeof CKEDITOR !== 'undefined') {
                    initProductForm();
                } else {
                    setTimeout(waitForCKEditor, 50);
                }
            }

            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(waitForCKEditor, 100);
                    initFileManager();
                });
            } else {
                setTimeout(waitForCKEditor, 100);
                initFileManager();
            }

            // Override Laravel File Manager to add window features
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.filemanager !== 'undefined') {
                const originalFilemanager = jQuery.fn.filemanager;
                jQuery.fn.filemanager = function(type, options) {
                    type = type || 'file';
                    const self = this;

                    this.on('click', function(e) {
                        e.preventDefault();
                        var route_prefix = (options && options.prefix) ? options.prefix : '/filemanager';
                        var target_input = $('#' + $(this).data('input'));
                        var target_preview = $('#' + $(this).data('preview'));

                        // Open window with better features (includes close button)
                        var fileManagerWindow = window.open(
                            route_prefix + '?type=' + type,
                            'FileManager',
                            'width=1200,height=800,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
                        );

                        // Focus on the new window
                        if (fileManagerWindow) {
                            fileManagerWindow.focus();
                        }

                        window.SetUrl = function(items) {
                            var file_path = items.map(function(item) {
                                return item.url;
                            }).join(',');

                            // set the value of the desired input to image url
                            target_input.val('').val(file_path).trigger('change');

                            // clear previous preview
                            target_preview.html('');

                            // set or change the preview image src
                            items.forEach(function(item) {
                                target_preview.append(
                                    $('<img>').css('height', '5rem').attr('src', item.thumb_url)
                                );
                            });

                            // trigger change event
                            target_preview.trigger('change');

                            // Close the file manager window after selection
                            if (fileManagerWindow && !fileManagerWindow.closed) {
                                fileManagerWindow.close();
                            }
                        };

                        return false;
                    });

                    return this;
                };
            }

            // Initialize Laravel File Manager
            function initFileManager() {
                // Wait for jQuery and LFM script to load
                if (typeof jQuery === 'undefined' || typeof jQuery.fn.filemanager === 'undefined') {
                    setTimeout(initFileManager, 100);
                    return;
                }

                const route_prefix = '{{ url('/filemanager') }}';

                // Initialize featured image file manager
                $('#lfm-featured').filemanager('image', {
                    prefix: route_prefix
                });

                // Initialize gallery images file manager
                // Create a hidden input for gallery selection
                const galleryInputId = 'gallery-selected-input';
                if ($('#' + galleryInputId).length === 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: galleryInputId
                    }).appendTo('#gallery-images-input');
                }

                // Create a hidden preview div
                const galleryPreviewId = 'gallery-selected-preview';
                if ($('#' + galleryPreviewId).length === 0) {
                    $('<div>').attr('id', galleryPreviewId).css('display', 'none').appendTo('#gallery-images-preview');
                }

                // Update button attributes
                $('#lfm-gallery').attr({
                    'data-input': galleryInputId,
                    'data-preview': galleryPreviewId
                });

                // Initialize file manager for gallery
                $('#lfm-gallery').filemanager('image', {
                    prefix: route_prefix
                });

                // Handle featured image selection
                const featuredInput = document.getElementById('featured-image-input');
                if (featuredInput) {
                    featuredInput.addEventListener('input', function() {
                        const path = this.value;
                        if (path) {
                            const fullUrl = '{{ url('/') }}/' + path.replace(/^\/+/, '');
                            // Update Alpine.js component
                            const galleryElement = document.querySelector('[x-data*="imageGallery"]');
                            if (galleryElement && galleryElement._x_dataStack && galleryElement._x_dataStack[0]) {
                                galleryElement._x_dataStack[0].featuredImage = fullUrl;
                            }
                        }
                    });
                }

                // Handle gallery images selection
                const gallerySelectedInput = document.getElementById('gallery-selected-input');
                if (gallerySelectedInput) {
                    $(gallerySelectedInput).on('change', function() {
                        const paths = $(this).val();
                        if (paths) {
                            // Split by comma if multiple images
                            const pathArray = paths.split(',').map(p => p.trim()).filter(p => p);

                            const galleryElement = document.querySelector('[x-data*="imageGallery"]');
                            if (galleryElement && galleryElement._x_dataStack && galleryElement._x_dataStack[0]) {
                                const galleryComponent = galleryElement._x_dataStack[0];

                                pathArray.forEach(path => {
                                    // Helper function to get full URL
                                    function getFullUrl(path) {
                                        if (!path) return null;
                                        const pathStr = String(path);
                                        // Check if already a full URL (starts with http:// or https://)
                                        if (/^https?:\/\//.test(pathStr)) {
                                            return pathStr;
                                        }
                                        // Otherwise, prepend base URL
                                        return '{{ url('/') }}/' + pathStr.replace(/^\/+/, '');
                                    }

                                    const fullUrl = getFullUrl(path);
                                    // Check if already exists
                                    const exists = galleryComponent.galleryImages.some(img => img.path ===
                                        path);
                                    if (!exists) {
                                        galleryComponent.galleryImages.push({
                                            url: fullUrl,
                                            path: path
                                        });

                                        // Create hidden input for form submission
                                        const hiddenInput = $('<input>').attr({
                                            type: 'hidden',
                                            name: 'gallery_images[]',
                                            value: path
                                        });
                                        $('#gallery-images-input').append(hiddenInput);
                                    }
                                });

                                // Clear the selection input for next selection
                                $(this).val('');
                            }
                        }
                    });
                }
            }


            // 4. Image Gallery Component
            function imageGallery() {
                return {
                    featuredImage: null,
                    galleryImages: [],
                    galleryPaths: [], // Store paths for gallery images

                    init() {
                        // Initialize Laravel File Manager for featured image
                        const lfmFeatured = document.getElementById('lfm-featured');
                        if (lfmFeatured) {
                            lfmFeatured.addEventListener('click', (e) => {
                                e.preventDefault();
                            });

                            // Use Laravel File Manager
                            if (typeof route !== 'undefined') {
                                lfmFeatured.setAttribute('data-url', '{{ url('/filemanager') }}?type=Images');
                            }
                        }

                        // Initialize Laravel File Manager for gallery images
                        const lfmGallery = document.getElementById('lfm-gallery');
                        if (lfmGallery) {
                            lfmGallery.addEventListener('click', (e) => {
                                e.preventDefault();
                            });

                            if (typeof route !== 'undefined') {
                                lfmGallery.setAttribute('data-url', '{{ url('/filemanager') }}?type=Images');
                            }
                        }

                        // Listen for featured image selection
                        const featuredInput = document.getElementById('featured-image-input');
                        if (featuredInput) {
                            featuredInput.addEventListener('change', () => {
                                const path = featuredInput.value;
                                if (path) {
                                    this.featuredImage = '{{ url('/') }}/' + path.replace(/^\/+/, '');
                                }
                            });
                        }

                        // Listen for gallery images selection
                        const galleryInput = document.getElementById('gallery-images-input');
                        if (galleryInput) {
                            // Check for changes in hidden inputs
                            const observer = new MutationObserver(() => {
                                this.updateGalleryImages();
                            });
                            observer.observe(galleryInput, {
                                childList: true,
                                subtree: true
                            });
                        }
                    },

                    updateGalleryImages() {
                        const galleryInput = document.getElementById('gallery-images-input');
                        if (!galleryInput) return;

                        const inputs = galleryInput.querySelectorAll('input[type="hidden"]');
                        this.galleryImages = [];
                        this.galleryPaths = [];

                        inputs.forEach(input => {
                            const path = input.value;
                            if (path) {
                                const fullUrl = '{{ url('/') }}/' + path.replace(/^\/+/, '');
                                this.galleryImages.push({
                                    url: fullUrl,
                                    path: path
                                });
                                this.galleryPaths.push(path);
                            }
                        });
                    },

                    removeFeatured() {
                        this.featuredImage = null;
                        const input = document.getElementById('featured-image-input');
                        if (input) {
                            input.value = '';
                        }
                        const preview = document.getElementById('featured-image-preview');
                        if (preview) {
                            preview.innerHTML = '';
                        }
                    },

                    removeGalleryImage(index) {
                        if (index < 0 || index >= this.galleryImages.length) return;

                        // Get the path before removing
                        const imageToRemove = this.galleryImages[index];
                        const pathToRemove = imageToRemove?.path || imageToRemove;

                        // Remove from arrays
                        this.galleryImages.splice(index, 1);
                        this.galleryPaths.splice(index, 1);

                        // Remove corresponding hidden input by value, not by index
                        const galleryInput = document.getElementById('gallery-images-input');
                        if (galleryInput && pathToRemove) {
                            const inputs = galleryInput.querySelectorAll('input[type="hidden"][name="gallery_images[]"]');
                            inputs.forEach(input => {
                                if (input.value === pathToRemove) {
                                    input.remove();
                                }
                            });
                        }
                    }
                }
            }

            // 5. Technical Specifications Component
            function technicalSpecs() {
                return {
                    specs: [],
                    addSpec() {
                        this.specs.push({
                            name: '',
                            value: ''
                        });
                    },
                    removeSpec(index) {
                        this.specs.splice(index, 1);
                    }
                }
            }

            // 6. Category Search
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('category-search');
                const categoryList = document.getElementById('category-list');
                const noResults = document.getElementById('category-no-results');
                const categoryItems = document.querySelectorAll('.category-item');
                const categoryContainer = categoryList ? categoryList.closest('div[style*="max-height"]') : null;

                if (searchInput) {
                    searchInput.addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase().trim();
                        let visibleCount = 0;

                        categoryItems.forEach(function(item) {
                            const categoryName = item.getAttribute('data-name');
                            if (categoryName.includes(searchTerm)) {
                                item.style.display = 'flex';
                                visibleCount++;
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        // Show/hide no results message
                        if (visibleCount === 0 && searchTerm !== '') {
                            noResults.classList.remove('hidden');
                            if (categoryList) categoryList.style.display = 'none';
                        } else {
                            noResults.classList.add('hidden');
                            if (categoryList) categoryList.style.display = 'block';
                        }
                    });
                }
            });
        </script>
    @endpush
</x-admin-layout>
