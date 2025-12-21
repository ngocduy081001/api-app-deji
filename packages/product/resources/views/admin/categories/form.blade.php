<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Danh mục sản phẩm</h1>
                <p class="mt-1 text-sm text-gray-600">Tổ chức sản phẩm của bạn theo danh mục</p>
            </div>
        </div>
    </x-slot>

    <!-- Success/Error Messages -->
    <div id="message-container" class="mb-6 hidden"></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Category Tree Sidebar (Left) -->
        <div class="lg:col-span-1">
            <x-admin.card title="Danh sách danh mục">

                <div class="max-h-[calc(100vh-300px)] overflow-y-auto" id="category-tree-container">
                    @if ($categoryTree->count() > 0)
                        <ul id="sortable-category-tree" class="space-y-1">
                            @foreach ($categoryTree as $cat)
                                @include('product::admin.categories.partials.tree-item', [
                                    'category' => $cat,
                                    'level' => 0,
                                    'currentId' => null,
                                ])
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">Chưa có danh mục nào</p>
                    @endif
                </div>
            </x-admin.card>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2">
            <x-admin.card id="form-card" title="Thêm danh mục mới">
                <form id="category-form">
                    <input type="hidden" id="category-id" name="id" value="">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục *</label>
                            <input type="text" name="name" id="category-name" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="Nhập tên danh mục">
                            <div id="error-name" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đường dẫn (Slug)</label>
                            <input type="text" name="slug" id="category-slug"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="tu-dong-tao">
                            <p class="mt-1 text-xs text-gray-500">Tự động tạo từ tên danh mục</p>
                            <div id="error-slug" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                            <textarea name="description" id="description" rows="6"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                placeholder="Mô tả về danh mục"></textarea>
                            <div id="error-description" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục cha</label>
                            <select name="parent_id" id="parent_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                                <option value="">Không có (Danh mục gốc)</option>
                                @foreach ($categoryTree as $cat)
                                    @include('product::admin.categories.partials.tree-option', [
                                        'category' => $cat,
                                        'level' => 0,
                                        'selected' => null,
                                    ])
                                @endforeach
                            </select>
                            <div id="error-parent_id" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div x-data="categoryImage()">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div x-show="imageUrl"
                                        class="relative w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-200">
                                        <img :src="imageUrl" alt="Category" class="w-full h-full object-cover">
                                        <button type="button" @click="removeImage()"
                                            class="absolute top-1 right-1 p-1 bg-red-600 text-white rounded-full hover:bg-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div x-show="!imageUrl"
                                        class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <input type="hidden" name="image" id="category-image-input" value="">
                                    <a id="lfm-category-image" data-input="category-image-input"
                                        data-preview="category-image-preview"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Chọn ảnh
                                    </a>
                                    <div id="category-image-preview" class="mt-2"></div>
                                    <p class="mt-1 text-xs text-gray-500">Chọn ảnh từ thư viện hoặc upload mới</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                    class="rounded border-gray-300 text-black focus:ring-black">
                                <label for="is_active" class="ml-2 text-sm text-gray-700">Kích hoạt</label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                    class="rounded border-gray-300 text-black focus:ring-black">
                                <label for="is_featured" class="ml-2 text-sm text-gray-700">Danh mục nổi bật</label>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <button type="button" id="btn-submit"
                                class="px-6 py-2 bg-black text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                Tạo danh mục
                            </button>
                        </div>
                    </div>
                </form>
            </x-admin.card>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
        <script>
            let currentCategoryId = null;
            let manualSlug = false;

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                initCategoryForm();
                initFileManager();
                initTreeToggle();
                initSortableTree();
                initAjaxHandlers();
            });

            // Auto-generate Slug
            function initCategoryForm() {
                const nameInput = document.getElementById('category-name');
                const slugInput = document.getElementById('category-slug');

                slugInput?.addEventListener('input', function() {
                    if (this.value !== '') manualSlug = true;
                });

                nameInput?.addEventListener('input', function() {
                    if (!manualSlug && slugInput) {
                        const slug = this.value
                            .toLowerCase()
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '')
                            .replace(/đ/g, 'd')
                            .replace(/[^\w\s-]/g, '')
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-')
                            .replace(/^-+|-+$/g, '');
                        slugInput.value = slug;
                    }
                });
            }

            // Tree toggle
            function initTreeToggle() {
                document.querySelectorAll('.tree-toggle').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const target = document.getElementById(this.getAttribute('data-target'));
                        const icon = this.querySelector('svg');
                        if (target) {
                            target.classList.toggle('hidden');
                            icon.classList.toggle('rotate-90');
                            if (!target.classList.contains('hidden')) {
                                setTimeout(initChildrenSortables, 50);
                            }
                        }
                    });
                });
            }

            // Sortable tree
            function initSortableTree() {
                const rootList = document.getElementById('sortable-category-tree');
                if (rootList) {
                    new Sortable(rootList, {
                        animation: 150,
                        handle: '.drag-handle',
                        ghostClass: 'opacity-50',
                        chosenClass: 'bg-blue-50',
                        group: 'category-tree',
                        onEnd: updateCategoryOrder
                    });
                }
                initChildrenSortables();
            }

            function initChildrenSortables() {
                document.querySelectorAll('.sortable-children').forEach(list => {
                    if (list.sortableInstance) return;
                    list.sortableInstance = new Sortable(list, {
                        animation: 150,
                        handle: '.drag-handle',
                        ghostClass: 'opacity-50',
                        chosenClass: 'bg-blue-50',
                        group: 'category-tree',
                        onEnd: updateCategoryOrder
                    });
                });
            }

            function updateCategoryOrder(evt) {
                // Collect all category IDs in the new order
                const siblings = Array.from(evt.to.children);
                const order = [];

                siblings.forEach((item) => {
                    const categoryId = item.getAttribute('data-id');
                    if (categoryId) {
                        order.push(parseInt(categoryId));
                    }
                });

                if (order.length === 0) {
                    setTimeout(initChildrenSortables, 100);
                    return;
                }

                // Determine parent_id from the target list
                let parentId = null;
                const targetList = evt.to;
                if (targetList.hasAttribute('data-parent-id')) {
                    const parentIdAttr = targetList.getAttribute('data-parent-id');
                    parentId = parentIdAttr && parentIdAttr !== '' ? parseInt(parentIdAttr) : null;
                }

                // Update sort_order and parent_id attributes for all items
                siblings.forEach((item, index) => {
                    item.setAttribute('data-sort-order', index + 1);
                    if (parentId !== null) {
                        item.setAttribute('data-parent-id', parentId);
                    }
                });

                // Update parent_id in form if category is being edited and moved to different parent
                const movedItemId = parseInt(evt.item.getAttribute('data-id'));
                if (currentCategoryId === movedItemId && parentId !== null) {
                    const parentSelect = document.getElementById('parent_id');
                    if (parentSelect) {
                        parentSelect.value = parentId;
                    }
                }

                // Send AJAX request to update order (silent update)
                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                order.forEach((id, index) => {
                    formData.append(`order[${index}]`, id);
                });
                if (parentId !== null) {
                    formData.append('parent_id', parentId);
                }

                fetch('{{ route('admin.categories.update-order') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .catch(error => {
                        console.error('Error updating order:', error);
                    });

                setTimeout(initChildrenSortables, 100);
            }

            // AJAX Handlers
            function initAjaxHandlers() {
                // New category button
                document.getElementById('btn-new-category')?.addEventListener('click', resetForm);

                // Submit button
                document.getElementById('btn-submit')?.addEventListener('click', submitForm);

                // Load category on tree item click
                document.addEventListener('click', function(e) {
                    const editLink = e.target.closest('.category-edit-link');
                    if (editLink) {
                        e.preventDefault();
                        const categoryId = editLink.getAttribute('data-id');
                        loadCategory(categoryId);
                    }
                });
            }

            // Reset form
            function resetForm() {
                currentCategoryId = null;
                document.getElementById('category-id').value = '';
                document.getElementById('category-form').reset();
                document.getElementById('category-name').value = '';
                document.getElementById('category-slug').value = '';
                document.getElementById('description').value = '';
                document.getElementById('parent_id').value = '';
                document.getElementById('category-image-input').value = '';
                document.getElementById('is_active').checked = true;
                document.getElementById('is_featured').checked = false;
                document.getElementById('form-card').querySelector('h3').textContent = 'Thêm danh mục mới';
                document.getElementById('btn-submit').textContent = 'Tạo danh mục';
                clearErrors();
                manualSlug = false;

                // Reset image
                const imageElement = document.querySelector('[x-data*="categoryImage"]');
                if (imageElement && imageElement._x_dataStack?.[0]) {
                    imageElement._x_dataStack[0].imageUrl = null;
                }

                // Update tree highlights
                document.querySelectorAll('.draggable-item').forEach(item => {
                    item.classList.remove('bg-blue-50', 'border-l-2', 'border-blue-500');
                });
            }

            // Load category for editing
            function loadCategory(categoryId) {
                fetch(`{{ route('admin.categories.get', ':id') }}`.replace(':id', categoryId), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const cat = data.data;
                            currentCategoryId = cat.id;
                            document.getElementById('category-id').value = cat.id;
                            document.getElementById('category-name').value = cat.name || '';
                            document.getElementById('category-slug').value = cat.slug || '';
                            document.getElementById('description').value = cat.description || '';
                            document.getElementById('category-image-input').value = cat.image || '';
                            document.getElementById('is_active').checked = cat.is_active ?? true;
                            document.getElementById('is_featured').checked = cat.is_featured ?? false;
                            document.getElementById('form-card').querySelector('h3').textContent = 'Chỉnh sửa danh mục';
                            document.getElementById('btn-submit').textContent = 'Cập nhật danh mục';
                            manualSlug = !!cat.slug;
                            clearErrors();

                            // Update image preview
                            const imageElement = document.querySelector('[x-data*="categoryImage"]');
                            if (imageElement && imageElement._x_dataStack?.[0] && cat.image) {
                                imageElement._x_dataStack[0].imageUrl = '{{ url('/') }}/' + cat.image.replace(/^\/+/,
                                    '');
                            }

                            // Reload tree to update parent selector and highlights
                            reloadTree().then(() => {
                                document.getElementById('parent_id').value = cat.parent_id || '';
                                // Update tree highlights
                                document.querySelectorAll('.draggable-item').forEach(item => {
                                    item.classList.remove('bg-blue-50', 'border-l-2', 'border-blue-500');
                                    if (item.getAttribute('data-id') == categoryId) {
                                        item.classList.add('bg-blue-50', 'border-l-2', 'border-blue-500');
                                    }
                                });
                            });
                        }
                    })
                    .catch(error => {
                        showMessage('Có lỗi xảy ra khi tải danh mục.', 'error');
                    });
            }

            // Submit form
            function submitForm() {
                const formData = new FormData();
                const tokenInput = document.querySelector('input[name="_token"]');
                if (!tokenInput) {
                    showMessage('Token không tìm thấy.', 'error');
                    return;
                }
                formData.append('_token', tokenInput.value);

                if (currentCategoryId) {
                    formData.append('_method', 'PUT');
                }

                // Collect form data with null checks
                const nameInput = document.getElementById('category-name');
                const slugInput = document.getElementById('category-slug');
                const descriptionInput = document.getElementById('description');
                const parentIdInput = document.getElementById('parent_id');
                const imageInput = document.getElementById('category-image-input');
                const isActiveInput = document.getElementById('is_active');
                const isFeaturedInput = document.getElementById('is_featured');

                if (!nameInput) {
                    showMessage('Trường tên danh mục không tìm thấy.', 'error');
                    return;
                }

                formData.append('name', nameInput.value || '');
                formData.append('slug', slugInput?.value || '');
                formData.append('description', descriptionInput?.value || '');
                formData.append('parent_id', parentIdInput?.value || '');
                formData.append('image', imageInput?.value || '');
                formData.append('is_active', isActiveInput?.checked ? '1' : '0');
                formData.append('is_featured', isFeaturedInput?.checked ? '1' : '0');

                const url = currentCategoryId ?
                    `{{ route('admin.categories.update', ':id') }}`.replace(':id', currentCategoryId) :
                    '{{ route('admin.categories.store') }}';

                fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showMessage(data.message, 'success');
                            reloadTree();
                            if (!currentCategoryId) {
                                resetForm();
                            }
                        } else {
                            showMessage(data.message || 'Có lỗi xảy ra.', 'error');
                            if (data.errors) {
                                displayErrors(data.errors);
                            }
                        }
                    })
                    .catch(error => {
                        showMessage('Có lỗi xảy ra khi lưu danh mục.', 'error');
                    });
            }

            // Delete category
            function deleteCategory(categoryId) {
                if (!confirm('Bạn có chắc chắn muốn xóa danh mục này?')) return;

                fetch(`{{ route('admin.categories.destroy', ':id') }}`.replace(':id', categoryId), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showMessage(data.message, 'success');
                            if (currentCategoryId == categoryId) {
                                resetForm();
                            }
                            reloadTree();
                        } else {
                            showMessage(data.message || 'Có lỗi xảy ra khi xóa.', 'error');
                        }
                    })
                    .catch(error => {
                        showMessage('Có lỗi xảy ra khi xóa danh mục.', 'error');
                    });
            }

            // Reload tree via AJAX
            function reloadTree() {
                return fetch('{{ route('admin.categories.index') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTree = doc.getElementById('category-tree-container');
                        const newParentSelect = doc.getElementById('parent_id');

                        if (newTree) {
                            const currentParentValue = document.getElementById('parent_id')?.value || '';
                            document.getElementById('category-tree-container').innerHTML = newTree.innerHTML;

                            // Update parent selector options
                            if (newParentSelect) {
                                document.getElementById('parent_id').innerHTML = newParentSelect.innerHTML;
                                document.getElementById('parent_id').value = currentParentValue;
                            }

                            initTreeToggle();
                            initSortableTree();
                        }
                    })
                    .catch(error => {
                        console.error('Error reloading tree:', error);
                        location.reload();
                    });
            }

            // Make deleteCategory globally available
            window.deleteCategory = deleteCategory;

            // Show message
            function showMessage(message, type) {
                const container = document.getElementById('message-container');
                container.className = `mb-6 px-4 py-3 rounded-md ${
                    type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'
                }`;
                container.textContent = message;
                container.classList.remove('hidden');
                setTimeout(() => container.classList.add('hidden'), 5000);
            }

            // Display errors
            function displayErrors(errors) {
                clearErrors();
                Object.keys(errors).forEach(key => {
                    const errorDiv = document.getElementById(`error-${key}`);
                    if (errorDiv) {
                        errorDiv.textContent = errors[key][0];
                        errorDiv.classList.remove('hidden');
                    }
                });
            }

            // Clear errors
            function clearErrors() {
                document.querySelectorAll('[id^="error-"]').forEach(el => {
                    el.classList.add('hidden');
                    el.textContent = '';
                });
            }

            // File Manager
            function initFileManager() {
                if (typeof jQuery === 'undefined' || typeof jQuery.fn.filemanager === 'undefined') {
                    setTimeout(initFileManager, 100);
                    return;
                }

                const route_prefix = '{{ url('/filemanager') }}';
                $('#lfm-category-image').filemanager('image', {
                    prefix: route_prefix
                });

                const imageInput = document.getElementById('category-image-input');
                if (imageInput) {
                    imageInput.addEventListener('input', function() {
                        const path = this.value;
                        if (path) {
                            const fullUrl = '{{ url('/') }}/' + path.replace(/^\/+/, '');
                            const imageElement = document.querySelector('[x-data*="categoryImage"]');
                            if (imageElement && imageElement._x_dataStack?.[0]) {
                                imageElement._x_dataStack[0].imageUrl = fullUrl;
                            }
                        }
                    });
                }
            }

            // Category Image Component
            function categoryImage() {
                return {
                    imageUrl: null,
                    init() {
                        const imageInput = document.getElementById('category-image-input');
                        if (imageInput?.value) {
                            this.imageUrl = '{{ url('/') }}/' + imageInput.value.replace(/^\/+/, '');
                        }
                        imageInput?.addEventListener('change', () => {
                            const path = imageInput.value;
                            if (path) {
                                this.imageUrl = '{{ url('/') }}/' + path.replace(/^\/+/, '');
                            }
                        });
                    },
                    removeImage() {
                        this.imageUrl = null;
                        document.getElementById('category-image-input').value = '';
                        document.getElementById('category-image-preview').innerHTML = '';
                    }
                }
            }
        </script>
    @endpush
</x-admin-layout>
