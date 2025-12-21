<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý Slider</h1>
                <p class="mt-1 text-sm text-gray-600">Tạo và quản lý slider items cho website</p>
            </div>
        </div>
    </x-slot>

    <!-- Slider Selector -->
    <x-admin.card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Chọn Slider</label>
                <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                    id="slider-selector">
                    @if ($sliders->isEmpty())
                        <option value="">Chưa có slider</option>
                    @else
                        @foreach ($sliders as $slider)
                            <option value="{{ $slider->id }}"
                                {{ $currentSlider && $currentSlider->id == $slider->id ? 'selected' : '' }}>
                                {{ $slider->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="flex items-end">
                <button type="button"
                    class="w-full px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-700 transition-colors flex items-center justify-center gap-2"
                    onclick="window.location.href='{{ route('admin.sliders.index') }}'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Quản lý slider
                </button>
            </div>
        </div>
    </x-admin.card>

    @if ($currentSlider)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Panel - Add Slider Items -->
            <div class="lg:col-span-1">
                <x-admin.card>
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Thêm Slider Item
                        </h3>
                    </div>
                    <div>
                        <div id="slider-form-container">
                            <div id="slider-form-header" class="mb-4 pb-2 border-b border-gray-200">
                                <h4 class="text-md font-semibold text-gray-900" id="slider-form-title">
                                    <span id="slider-form-title-text">Thêm Slider Item</span>
                                    <button type="button" id="slider-form-cancel-btn" onclick="cancelEdit()"
                                        class="hidden ml-2 text-sm text-gray-600 hover:text-gray-800">
                                        (Hủy)
                                    </button>
                                </h4>
                            </div>
                            <form id="slider-item-form" method="POST" action="{{ route('admin.slides.store') }}">
                                @csrf
                                <input type="hidden" name="_method" id="slider-form-method" value="POST">
                                <input type="hidden" name="slider_item_id" id="slider-form-id" value="">
                                <input type="hidden" name="slider_id" value="{{ $currentSlider->id }}">

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
                                    <input type="text" name="title" id="slider-title-input"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                        placeholder="Tiêu đề slider item">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                                    <textarea name="description" id="slider-description-input" rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                        placeholder="Mô tả slider item"></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh Desktop <span
                                            class="text-red-600">*</span></label>
                                    <input type="hidden" name="image" id="slider-image-input" value="">
                                    <div class="flex items-center gap-2">
                                        <a id="lfm-slider-image" data-input="slider-image-input"
                                            data-preview="slider-image-preview"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Chọn ảnh
                                        </a>
                                        <button type="button" onclick="clearSliderImage()"
                                            class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md hover:bg-gray-50">
                                            Xóa
                                        </button>
                                    </div>
                                    <div id="slider-image-preview" class="mt-2"></div>
                                    <p class="mt-1 text-xs text-gray-500">Chọn ảnh từ thư viện hoặc upload mới</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh Mobile</label>
                                    <input type="hidden" name="image_mobile" id="slider-image-mobile-input"
                                        value="">
                                    <div class="flex items-center gap-2">
                                        <a id="lfm-slider-image-mobile" data-input="slider-image-mobile-input"
                                            data-preview="slider-image-mobile-preview"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Chọn ảnh
                                        </a>
                                        <button type="button" onclick="clearSliderImageMobile()"
                                            class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md hover:bg-gray-50">
                                            Xóa
                                        </button>
                                    </div>
                                    <div id="slider-image-mobile-preview" class="mt-2"></div>
                                    <p class="mt-1 text-xs text-gray-500">Chọn ảnh từ thư viện hoặc upload mới</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Link</label>
                                    <input type="text" name="link" id="slider-link-input"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                        placeholder="https://example.com">
                                </div>

                                <button type="submit" id="slider-submit-btn"
                                    class="w-full px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-700 transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span id="slider-submit-text">Thêm vào slider</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Right Panel - Slider Items Structure -->
            <div class="lg:col-span-2">
                <x-admin.card>
                    <div class="mb-4 pb-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Danh sách Slider Items
                        </h3>
                        <button type="button"
                            class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-700 transition-colors text-sm flex items-center gap-2"
                            onclick="saveSliderOrder()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Lưu thứ tự
                        </button>
                    </div>
                    @if ($sliderItems->isEmpty())
                        <div
                            class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-md flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Chưa có slider item nào. Vui lòng thêm slider item từ panel bên trái.
                        </div>
                    @else
                        <div id="slider-items-list" class="slider-items-list">
                            @foreach ($sliderItems as $item)
                                @include('settings::admin.slides.partials.slider-item-sortable', [
                                    'item' => $item,
                                ])
                            @endforeach
                        </div>
                    @endif
                </x-admin.card>
            </div>
        </div>
    @else
        <div
            class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-md flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Vui lòng tạo slider trước khi quản lý slider items.
            <a href="{{ route('admin.sliders.index') }}" class="text-yellow-900 underline hover:text-yellow-700">Đi
                tới quản lý slider</a>
        </div>
    @endif

    @push('styles')
        <style>
            /* Slider Items List Styles */
            .slider-items-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .slider-item {
                background: #fff;
                border: 1px solid #dee2e6;
                border-radius: 5px;
                padding: 12px 15px;
                margin-bottom: 10px;
                position: relative;
                cursor: move;
                transition: all 0.2s;
            }

            .slider-item:hover {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border-color: #0d6efd;
            }

            .slider-item.dragging {
                opacity: 0.5;
            }

            .slider-item-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .slider-item-title {
                display: flex;
                align-items: center;
                gap: 10px;
                flex: 1;
            }

            .slider-item-handle {
                cursor: move;
                color: #6c757d;
                margin-right: 5px;
            }

            .slider-item-info {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .slider-item-name {
                font-weight: 600;
                color: #212529;
            }

            .slider-item-description {
                font-size: 0.875rem;
                color: #6c757d;
            }

            .slider-item-actions {
                display: flex;
                gap: 5px;
            }

            .slider-item-actions button {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
                border-radius: 0.375rem;
                border: 1px solid;
                transition: all 0.2s;
            }

            .slider-item-actions .btn-edit {
                color: #3b82f6;
                border-color: #3b82f6;
                background-color: transparent;
            }

            .slider-item-actions .btn-edit:hover {
                background-color: #3b82f6;
                color: white;
            }

            .slider-item-actions .btn-delete {
                color: #ef4444;
                border-color: #ef4444;
                background-color: transparent;
            }

            .slider-item-actions .btn-delete:hover {
                background-color: #ef4444;
                color: white;
            }

            .sortable-ghost {
                opacity: 0.4;
                background: #f8f9fa;
            }

            .sortable-drag {
                opacity: 0.8;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Slider selector change
                document.getElementById('slider-selector')?.addEventListener('change', function() {
                    if (this.value) {
                        window.location.href = '{{ route('admin.slides.index') }}?slider_id=' + this.value;
                    }
                });

                // Initialize Laravel File Manager
                initFileManager();

                // Initialize Sortable for slider items
                initializeSortable();
            });

            function initFileManager() {
                // Wait for jQuery and LFM script to load
                if (typeof jQuery === 'undefined' || typeof jQuery.fn.filemanager === 'undefined') {
                    setTimeout(initFileManager, 100);
                    return;
                }

                const route_prefix = '{{ url('/filemanager') }}';

                // Initialize file manager for slider images
                $('#lfm-slider-image').filemanager('image', {
                    prefix: route_prefix
                });

                $('#lfm-slider-image-mobile').filemanager('image', {
                    prefix: route_prefix
                });

                // Handle image selection
                const sliderImageInput = document.getElementById('slider-image-input');
                if (sliderImageInput) {
                    sliderImageInput.addEventListener('change', function() {
                        updateImagePreview('slider-image-input', 'slider-image-preview');
                    });
                }

                const sliderImageMobileInput = document.getElementById('slider-image-mobile-input');
                if (sliderImageMobileInput) {
                    sliderImageMobileInput.addEventListener('change', function() {
                        updateImagePreview('slider-image-mobile-input', 'slider-image-mobile-preview');
                    });
                }
            }

            function updateImagePreview(inputId, previewId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                if (!input || !preview) return;

                const imagePath = input.value;
                if (imagePath) {
                    let imageUrl = imagePath;
                    if (!imagePath.startsWith('http') && !imagePath.startsWith('/')) {
                        imageUrl = '{{ asset('storage/') }}/' + imagePath.replace(/^\//, '');
                    } else if (!imagePath.startsWith('http')) {
                        imageUrl = '{{ asset('') }}' + imagePath;
                    }
                    preview.innerHTML = '<img src="' + imageUrl +
                        '" alt="Image preview" class="w-32 h-20 object-cover rounded border border-gray-300">';
                } else {
                    preview.innerHTML = '';
                }
            }

            function clearSliderImage() {
                document.getElementById('slider-image-input').value = '';
                document.getElementById('slider-image-preview').innerHTML = '';
            }

            function clearSliderImageMobile() {
                document.getElementById('slider-image-mobile-input').value = '';
                document.getElementById('slider-image-mobile-preview').innerHTML = '';
            }

            function initializeSortable() {
                const sliderItemsList = document.getElementById('slider-items-list');
                if (!sliderItemsList) return;

                const sortableOptions = {
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    handle: '.slider-item-handle',
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        console.log('Slider items order changed');
                    }
                };

                new Sortable(sliderItemsList, sortableOptions);
            }

            function saveSliderOrder() {
                const sliderItemsList = document.getElementById('slider-items-list');
                if (!sliderItemsList) return;

                const items = [];
                const children = sliderItemsList.children;

                Array.from(children).forEach((item, index) => {
                    if (item.classList.contains('slider-item')) {
                        const itemId = parseInt(item.dataset.id);
                        items.push({
                            id: itemId,
                            order: index
                        });
                    }
                });

                fetch('{{ route('admin.slides.update-order') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            slider_id: {{ $currentSlider?->id ?? 'null' }},
                            order: items
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Đã lưu thứ tự slider items thành công!');
                            location.reload();
                        } else {
                            alert('Có lỗi xảy ra: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi lưu thứ tự slider items');
                    });
            }

            function editSliderItem(itemId, itemData) {
                const form = document.getElementById('slider-item-form');
                const formContainer = document.getElementById('slider-form-container');

                // Update form to edit mode
                document.getElementById('slider-form-id').value = itemId;
                document.getElementById('slider-form-method').value = 'PUT';
                form.action = '{{ route('admin.slides.update', ':id') }}'.replace(':id', itemId);

                // Update form title
                document.getElementById('slider-form-title-text').textContent = 'Chỉnh sửa Slider Item';
                document.getElementById('slider-form-cancel-btn').classList.remove('hidden');

                // Fill form data
                document.getElementById('slider-title-input').value = itemData.title || '';
                document.getElementById('slider-description-input').value = itemData.description || '';
                document.getElementById('slider-image-input').value = itemData.image || '';
                document.getElementById('slider-image-mobile-input').value = itemData.image_mobile || '';
                document.getElementById('slider-link-input').value = itemData.link || '';

                // Update image previews
                updateImagePreview('slider-image-input', 'slider-image-preview');
                updateImagePreview('slider-image-mobile-input', 'slider-image-mobile-preview');

                // Update submit button
                document.getElementById('slider-submit-text').textContent = 'Cập nhật slider item';

                // Scroll to form
                formContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // Focus on title input
                setTimeout(() => {
                    document.getElementById('slider-title-input').focus();
                }, 300);
            }

            function cancelEdit() {
                const form = document.getElementById('slider-item-form');

                // Reset form to create mode
                document.getElementById('slider-form-id').value = '';
                document.getElementById('slider-form-method').value = 'POST';
                form.action = '{{ route('admin.slides.store') }}';

                // Reset form title
                document.getElementById('slider-form-title-text').textContent = 'Thêm Slider Item';
                document.getElementById('slider-form-cancel-btn').classList.add('hidden');

                // Reset form fields
                form.reset();
                document.getElementById('slider-image-input').value = '';
                document.getElementById('slider-image-preview').innerHTML = '';
                document.getElementById('slider-image-mobile-input').value = '';
                document.getElementById('slider-image-mobile-preview').innerHTML = '';

                // Update submit button
                document.getElementById('slider-submit-text').textContent = 'Thêm vào slider';
            }

            function deleteSliderItem(itemId, itemTitle) {
                if (confirm('Bạn có chắc muốn xóa slider item "' + (itemTitle || 'này') + '"?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.slides.destroy', ':id') }}'.replace(':id', itemId);

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
    @endpush
</x-admin-layout>
