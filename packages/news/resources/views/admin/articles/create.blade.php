<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tạo bài viết mới</h1>
                <p class="mt-1 text-sm text-gray-600">Viết bài viết hoặc tin tức mới</p>
            </div>
            <a href="{{ route('admin.articles.index') }}">
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

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-6">
            <x-admin.alert type="error">
                <div class="font-medium">Vui lòng sửa các lỗi sau:</div>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-admin.alert>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.articles.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <x-admin.card title="Thông tin bài viết">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                            <input type="text" name="title" id="article-title" value="{{ old('title') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('title') border-red-500 @enderror"
                                placeholder="Nhập tiêu đề bài viết">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đường dẫn (Slug)</label>
                            <input type="text" name="slug" id="article-slug" value="{{ old('slug') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('slug') border-red-500 @enderror"
                                placeholder="tu-dong-tao">
                            <p class="mt-1 text-xs text-gray-500">Tự động tạo từ tiêu đề nếu để trống</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tóm tắt</label>
                            <textarea name="excerpt" id="article-excerpt" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('excerpt') border-red-500 @enderror"
                                placeholder="Tóm tắt ngắn gọn về bài viết">{{ old('excerpt') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Tóm tắt ngắn (tối đa 1000 ký tự)</p>
                            @error('excerpt')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung</label>
                            <textarea name="content" id="article-content" rows="15"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Featured Image -->
                <x-admin.card title="Hình ảnh đại diện">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL hình ảnh đại diện</label>
                            <div class="flex gap-2">
                                <input type="text" name="featured_image" id="featured-image"
                                    value="{{ old('featured_image') }}"
                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('featured_image') border-red-500 @enderror"
                                    placeholder="Nhập URL hình ảnh hoặc sử dụng file manager">
                                <button type="button" id="lfm-featured-image" data-input="featured-image"
                                    data-preview="featured-image-preview"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-black">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                            @error('featured_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="featured-image-preview" class="mt-2">
                            @if (old('featured_image'))
                                <img src="{{ old('featured_image') }}" alt="Xem trước hình ảnh đại diện"
                                    class="max-w-full h-48 object-cover rounded-md border border-gray-300">
                            @endif
                        </div>
                    </div>
                </x-admin.card>

                <!-- Tags -->
                <x-admin.card title="Thẻ tag">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thẻ tag</label>
                            <input type="text" name="tags_input" id="article-tags" value="{{ old('tags_input') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="Nhập các thẻ tag cách nhau bởi dấu phẩy">
                            <p class="mt-1 text-xs text-gray-500">Phân cách các thẻ bằng dấu phẩy (ví dụ: tin tức, công
                                nghệ,
                                blog)</p>
                            <input type="hidden" name="tags" id="tags-json" value="{{ old('tags', '[]') }}">
                        </div>
                    </div>
                </x-admin.card>

                <!-- SEO -->
                <x-admin.card title="SEO">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                            <input type="text" name="meta_title" id="meta-title" value="{{ old('meta_title') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('meta_title') border-red-500 @enderror"
                                placeholder="Tiêu đề SEO (tối đa 60 ký tự)">
                            <p class="mt-1 text-xs text-gray-500">Để trống sẽ sử dụng tiêu đề bài viết. Tối đa 60 ký
                                tự.</p>
                            @error('meta_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <textarea name="meta_description" id="meta-description" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('meta_description') border-red-500 @enderror"
                                placeholder="Mô tả SEO (tối đa 160 ký tự)">{{ old('meta_description') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Để trống sẽ sử dụng tóm tắt bài viết. Tối đa 160 ký
                                tự.</p>
                            @error('meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta-keywords"
                                value="{{ old('meta_keywords') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('meta_keywords') border-red-500 @enderror"
                                placeholder="Từ khóa SEO cách nhau bởi dấu phẩy">
                            <p class="mt-1 text-xs text-gray-500">Phân cách các từ khóa bằng dấu phẩy (ví dụ: tin tức,
                                blog, seo)</p>
                            @error('meta_keywords')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish -->
                <x-admin.card title="Xuất bản">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select name="status" id="article-status"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('status') border-red-500 @enderror">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Bản
                                    nháp
                                </option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>
                                    Đã xuất bản</option>
                                <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Đã lưu
                                    trữ
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày xuất bản</label>
                            <input type="datetime-local" name="published_at" id="published-at"
                                value="{{ old('published_at') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('published_at') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Để trống để xuất bản ngay lập tức</p>
                            @error('published_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                {{ old('is_featured') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-black focus:ring-black">
                            <label for="is_featured" class="ml-2 text-sm text-gray-700">Bài viết nổi bật</label>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <x-admin.button type="submit" class="w-full">
                                Tạo bài viết
                            </x-admin.button>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Category -->
                <x-admin.card title="Chuyên mục">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chuyên mục</label>
                            <select name="category_id" id="article-category"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('category_id') border-red-500 @enderror">
                                <option value="">Không có chuyên mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>
    </form>

    @push('scripts')
        <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
        <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
        <script>
            // Function to initialize everything after CKEditor is loaded
            function initArticleForm() {
                // 1. Auto-generate Slug
                const titleInput = document.getElementById('article-title');
                const slugInput = document.getElementById('article-slug');
                let manualSlug = false;

                // Check if slug was manually edited
                if (slugInput) {
                    slugInput.addEventListener('input', function() {
                        if (this.value !== '') {
                            manualSlug = true;
                        }
                    });
                }

                if (titleInput) {
                    titleInput.addEventListener('input', function() {
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

                // 2. Initialize CKEditor with Laravel File Manager
                if (typeof CKEDITOR !== 'undefined') {
                    var fileManagerUrl = '{{ url('/filemanager') }}';

                    // Content Editor
                    CKEDITOR.replace('article-content', {
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

                // 3. Initialize Laravel File Manager for featured image
                if (typeof lfm !== 'undefined') {
                    document.getElementById('lfm-featured-image').addEventListener('click', function(e) {
                        e.preventDefault();
                        lfm('lfm-featured-image', 'image', {
                            prefix: '/filemanager'
                        });
                    });
                }

                // 4. Update featured image preview
                const featuredImageInput = document.getElementById('featured-image');
                const featuredImagePreview = document.getElementById('featured-image-preview');
                if (featuredImageInput && featuredImagePreview) {
                    featuredImageInput.addEventListener('input', function() {
                        if (this.value) {
                            featuredImagePreview.innerHTML =
                                '<img src="' + this.value +
                                '" alt="Xem trước hình ảnh đại diện" class="max-w-full h-48 object-cover rounded-md border border-gray-300">';
                        } else {
                            featuredImagePreview.innerHTML = '';
                        }
                    });
                }

                // 5. Handle tags input - convert comma-separated to JSON array
                const tagsInput = document.getElementById('article-tags');
                const tagsJsonInput = document.getElementById('tags-json');
                if (tagsInput && tagsJsonInput) {
                    // Update tags when input changes
                    function updateTags() {
                        if (!tagsInput.value || tagsInput.value.trim() === '') {
                            // If empty, send empty array
                            tagsJsonInput.value = JSON.stringify([]);
                        } else {
                            const tags = tagsInput.value
                                .split(',')
                                .map(tag => tag.trim())
                                .filter(tag => tag.length > 0);
                            tagsJsonInput.value = JSON.stringify(tags);
                        }
                    }

                    tagsInput.addEventListener('input', updateTags);
                    tagsInput.addEventListener('blur', updateTags);

                    // Initialize tags from old input if exists
                    updateTags();
                }
            }

            // Wait for DOM and CKEditor to be ready
            function waitForCKEditor() {
                if (typeof CKEDITOR !== 'undefined') {
                    initArticleForm();
                } else {
                    setTimeout(waitForCKEditor, 100);
                }
            }

            // Start initialization when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', waitForCKEditor);
            } else {
                waitForCKEditor();
            }
        </script>
    @endpush
</x-admin-layout>
