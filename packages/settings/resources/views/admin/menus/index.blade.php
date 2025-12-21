<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý Menu</h1>
                <p class="mt-1 text-sm text-gray-600">Tạo và quản lý cấu trúc menu cho website</p>
            </div>
        </div>
    </x-slot>

    <!-- Menu Group Selector -->
    <x-admin.card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Chọn Nhóm Menu</label>
                <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                    id="menu-group-selector">
                    @if ($menuGroups->isEmpty())
                        <option value="">Chưa có nhóm menu</option>
                    @else
                        @foreach ($menuGroups as $group)
                            <option value="{{ $group->id }}"
                                {{ $currentGroup && $currentGroup->id == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="flex items-end">
                <button type="button"
                    class="w-full px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-700 transition-colors flex items-center justify-center gap-2"
                    onclick="window.location.href='{{ route('admin.menu-groups.index') }}'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Quản lý nhóm menu
                </button>
            </div>
        </div>
    </x-admin.card>

    @if ($currentGroup)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Panel - Add Menu Items -->
            <div class="lg:col-span-1">
                <x-admin.card>
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Thêm Menu Item
                        </h3>
                    </div>
                    <div>
                        <!-- Categories Form -->
                        <div id="menu-form-container">
                            <div id="menu-form-header" class="mb-4 pb-2 border-b border-gray-200">
                                <h4 class="text-md font-semibold text-gray-900" id="menu-form-title">
                                    <span id="menu-form-title-text">Thêm Menu Item</span>
                                    <button type="button" id="menu-form-cancel-btn" onclick="cancelEdit()"
                                        class="hidden ml-2 text-sm text-gray-600 hover:text-gray-800">
                                        (Hủy)
                                    </button>
                                </h4>
                            </div>
                            <form id="category-form" method="POST" action="{{ route('admin.menus.store') }}">
                                @csrf
                                <input type="hidden" name="_method" id="menu-form-method" value="POST">
                                <input type="hidden" name="menu_id" id="menu-form-id" value="">
                                <input type="hidden" name="menu_group_id" value="{{ $currentGroup->id }}">
                                <input type="hidden" name="type" value="category">
                                <input type="hidden" name="category_id" id="menu-category-id-hidden" value="">

                                <div class="mb-4" id="category-select-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn danh mục <span
                                            class="text-red-600">*</span></label>
                                    <select name="category_id" id="menu-category-select"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                        required>
                                        <option value="">-- Chọn danh mục --</option>
                                        @php
                                            $categories = \Vendor\Product\Models\ProductCategory::orderBy(
                                                'name',
                                            )->get();
                                        @endphp
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                data-category-name="{{ $category->name }}">{{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if (count($usedCategoryIds ?? []) > 0)
                                        <p class="mt-1 text-xs text-gray-500" id="category-info-text">
                                            {{ count($usedCategoryIds) }} danh mục đã được sử dụng và không hiển thị
                                            trong danh sách
                                        </p>
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên menu <span
                                            class="text-red-600">*</span></label>
                                    <input type="text" name="name" id="menu-name-input"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                        placeholder="Tên hiển thị" required>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (tùy chọn)</label>
                                    <input type="hidden" name="icon" id="menu-icon-input" value="">
                                    <div class="flex items-center gap-2">
                                        <a id="lfm-menu-icon" data-input="menu-icon-input"
                                            data-preview="menu-icon-preview"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Chọn ảnh
                                        </a>
                                        <button type="button" onclick="clearMenuIcon()"
                                            class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md hover:bg-gray-50">
                                            Xóa
                                        </button>
                                    </div>
                                    <div id="menu-icon-preview" class="mt-2"></div>
                                    <p class="mt-1 text-xs text-gray-500">Chọn ảnh từ thư viện hoặc upload mới</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Target</label>
                                    <select name="target" id="menu-target-select"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                                        <option value="_self">Mở ở tab hiện tại</option>
                                        <option value="_blank">Mở tab mới</option>
                                    </select>
                                </div>

                                <div class="mb-4 flex items-center">
                                    <input type="checkbox" name="is_active"
                                        class="rounded border-gray-300 text-black focus:ring-black"
                                        id="menu-active-checkbox" value="1" checked>
                                    <label class="ml-2 text-sm text-gray-700" for="menu-active-checkbox">Kích
                                        hoạt</label>
                                </div>

                                <button type="submit" id="menu-submit-btn"
                                    class="w-full px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-700 transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span id="menu-submit-text">Thêm vào menu</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Right Panel - Menu Structure -->
            <div class="lg:col-span-2">
                <x-admin.card>
                    <div class="mb-4 pb-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Cấu trúc Menu
                        </h3>
                        <button type="button"
                            class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-700 transition-colors text-sm flex items-center gap-2"
                            onclick="saveMenuOrder()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Lưu thứ tự
                        </button>
                    </div>
                    <div></div>
                    @if ($menus->isEmpty())
                        <div
                            class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-md flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Chưa có menu nào. Vui lòng thêm menu từ panel bên trái.
                        </div>
                    @else
                        <div id="menu-tree" class="menu-tree">
                            @foreach ($menus as $menu)
                                @include('settings::admin.menus.partials.menu-item-sortable', [
                                    'menu' => $menu,
                                    'level' => 0,
                                ])
                            @endforeach
                        </div>
                    @endif
            </div>
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
            Vui lòng tạo nhóm menu trước khi quản lý menu.
            <a href="{{ route('admin.menu-groups.index') }}"
                class="text-yellow-900 underline hover:text-yellow-700">Đi
                tới quản lý nhóm menu</a>
        </div>
    @endif


    @push('styles')
        <style>
            /* Menu Tree Styles */
            .menu-tree {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .menu-item {
                background: #fff;
                border: 1px solid #dee2e6;
                border-radius: 5px;
                padding: 12px 15px;
                margin-bottom: 10px;
                position: relative;
                cursor: move;
                transition: all 0.2s;
            }

            .menu-item:hover {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border-color: #0d6efd;
            }

            .menu-item.dragging {
                opacity: 0.5;
            }

            .menu-item-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .menu-item-title {
                display: flex;
                align-items: center;
                gap: 10px;
                flex: 1;
            }

            .menu-item-handle {
                cursor: move;
                color: #6c757d;
                margin-right: 5px;
            }

            .menu-item-info {
                display: flex;
                flex-direction: column;
            }

            .menu-item-name {
                font-weight: 600;
                color: #212529;
            }

            .menu-item-type {
                font-size: 0.875rem;
                color: #6c757d;
            }

            .menu-item-actions {
                display: flex;
                gap: 5px;
            }

            .menu-item-actions button {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
                border-radius: 0.375rem;
                border: 1px solid;
                transition: all 0.2s;
            }

            .menu-item-actions .btn-edit {
                color: #3b82f6;
                border-color: #3b82f6;
                background-color: transparent;
            }

            .menu-item-actions .btn-edit:hover {
                background-color: #3b82f6;
                color: white;
            }

            .menu-item-actions .btn-delete {
                color: #ef4444;
                border-color: #ef4444;
                background-color: transparent;
            }

            .menu-item-actions .btn-delete:hover {
                background-color: #ef4444;
                color: white;
            }

            .menu-children {
                list-style: none;
                padding-left: 30px;
                margin-top: 10px;
                margin-bottom: 0;
                border-left: 2px dashed #dee2e6;
            }

            .menu-item.inactive {
                opacity: 0.6;
                background-color: #f8f9fa;
            }

            .badge-menu-type {
                font-size: 0.75rem;
                padding: 0.25em 0.6em;
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
                // Menu group selector change
                document.getElementById('menu-group-selector')?.addEventListener('change', function() {
                    if (this.value) {
                        window.location.href = '{{ route('admin.menus.index') }}?menu_group_id=' + this.value;
                    }
                });

                // Initialize Laravel File Manager
                initFileManager();

                // Initialize Sortable for menu tree
                initializeSortable();
            });

            function initFileManager() {
                // Wait for jQuery and LFM script to load
                if (typeof jQuery === 'undefined' || typeof jQuery.fn.filemanager === 'undefined') {
                    setTimeout(initFileManager, 100);
                    return;
                }

                const route_prefix = '{{ url('/filemanager') }}';

                // Initialize file manager for menu icon
                $('#lfm-menu-icon').filemanager('image', {
                    prefix: route_prefix
                });

                // Handle icon selection
                const menuIconInput = document.getElementById('menu-icon-input');
                if (menuIconInput) {
                    menuIconInput.addEventListener('change', function() {
                        updateIconPreview('menu-icon-input', 'menu-icon-preview');
                    });
                }

                // Sync category select to hidden input
                const categorySelect = document.getElementById('menu-category-select');
                const categoryHidden = document.getElementById('menu-category-id-hidden');
                if (categorySelect && categoryHidden) {
                    categorySelect.addEventListener('change', function() {
                        categoryHidden.value = this.value;
                    });
                }

                // Handle form submit to preserve scroll position
                const categoryForm = document.getElementById('category-form');
                if (categoryForm) {
                    categoryForm.addEventListener('submit', function(e) {
                        // Sync category_id from select to hidden input before submit
                        if (categorySelect && categoryHidden) {
                            categoryHidden.value = categorySelect.value;
                        }

                        // Save scroll position before submit
                        sessionStorage.setItem('menuFormScrollPosition', window.pageYOffset || document.documentElement
                            .scrollTop);
                    });
                }
            }

            function updateIconPreview(inputId, previewId) {
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
                        '" alt="Icon preview" class="w-20 h-20 object-cover rounded border border-gray-300">';
                } else {
                    preview.innerHTML = '';
                }
            }

            function clearMenuIcon() {
                document.getElementById('menu-icon-input').value = '';
                document.getElementById('menu-icon-preview').innerHTML = '';
            }

            // Restore scroll position after page load
            window.addEventListener('load', function() {
                const savedPosition = sessionStorage.getItem('menuFormScrollPosition') || sessionStorage.getItem(
                    'menuEditScrollPosition');
                if (savedPosition) {
                    window.scrollTo(0, parseInt(savedPosition));
                    sessionStorage.removeItem('menuFormScrollPosition');
                    sessionStorage.removeItem('menuEditScrollPosition');
                }
            });

            function initializeSortable() {
                const menuTree = document.getElementById('menu-tree');
                if (!menuTree) return;

                const sortableOptions = {
                    group: 'nested',
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    handle: '.menu-item-handle',
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        // Optional: Show save button notification
                        console.log('Menu order changed');
                    }
                };

                // Initialize sortable for root menu
                new Sortable(menuTree, sortableOptions);

                // Initialize sortable for all child menus
                document.querySelectorAll('.menu-children').forEach(function(el) {
                    new Sortable(el, sortableOptions);
                });
            }

            function saveMenuOrder() {
                const menuTree = document.getElementById('menu-tree');
                if (!menuTree) return;

                const menuOrder = getMenuOrder(menuTree);

                fetch('{{ route('admin.menus.update-order') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            menu_group_id: {{ $currentGroup?->id ?? 'null' }},
                            order: menuOrder
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Đã lưu thứ tự menu thành công!');
                            location.reload();
                        } else {
                            alert('Có lỗi xảy ra: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi lưu thứ tự menu');
                    });
            }

            function getMenuOrder(container, parentId = null) {
                const items = [];
                const children = container.children;

                Array.from(children).forEach((item, index) => {
                    if (item.classList.contains('menu-item')) {
                        const menuId = parseInt(item.dataset.id);
                        const menuData = {
                            id: menuId,
                            order: index,
                            parent_id: parentId
                        };

                        items.push(menuData);

                        // Check for children
                        const childrenContainer = item.querySelector('.menu-children');
                        if (childrenContainer) {
                            const childItems = getMenuOrder(childrenContainer, menuId);
                            items.push(...childItems);
                        }
                    }
                });

                return items;
            }

            function editMenu(menuId, menuData) {
                const form = document.getElementById('category-form');
                const formContainer = document.getElementById('menu-form-container');

                // Save scroll position
                sessionStorage.setItem('menuEditScrollPosition', window.pageYOffset || document.documentElement.scrollTop);

                // Update form to edit mode
                document.getElementById('menu-form-id').value = menuId;
                document.getElementById('menu-form-method').value = 'PUT';
                form.action = '/admin/menus/' + menuId;

                // Update form title
                document.getElementById('menu-form-title-text').textContent = 'Chỉnh sửa Menu';
                document.getElementById('menu-form-cancel-btn').classList.remove('hidden');

                // Disable category select when editing (can't change category)
                const categorySelect = document.getElementById('menu-category-select');
                const categoryHidden = document.getElementById('menu-category-id-hidden');
                if (categorySelect) {
                    categorySelect.disabled = true;
                    categorySelect.style.opacity = '0.6';
                    categorySelect.style.cursor = 'not-allowed';

                    // Set current category value (if available)
                    if (menuData.category_id) {
                        categorySelect.value = menuData.category_id;
                        if (categoryHidden) {
                            categoryHidden.value = menuData.category_id;
                        }
                    }
                }

                // Update category info text
                const categoryInfo = document.getElementById('category-info-text');
                if (categoryInfo) {
                    categoryInfo.textContent = 'Không thể thay đổi danh mục khi chỉnh sửa';
                    categoryInfo.classList.add('text-yellow-600');
                }

                // Fill form data
                document.getElementById('menu-name-input').value = menuData.name || '';
                document.getElementById('menu-icon-input').value = menuData.icon || '';
                document.getElementById('menu-target-select').value = menuData.target || '_self';
                document.getElementById('menu-active-checkbox').checked = menuData.is_active;

                // Update icon preview
                updateIconPreview('menu-icon-input', 'menu-icon-preview');

                // Update submit button
                document.getElementById('menu-submit-text').textContent = 'Cập nhật menu';

                // Scroll to form
                formContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // Focus on name input
                setTimeout(() => {
                    document.getElementById('menu-name-input').focus();
                }, 300);
            }

            function cancelEdit() {
                const form = document.getElementById('category-form');

                // Reset form to create mode
                document.getElementById('menu-form-id').value = '';
                document.getElementById('menu-form-method').value = 'POST';
                form.action = '{{ route('admin.menus.store') }}';

                // Reset form title
                document.getElementById('menu-form-title-text').textContent = 'Thêm Menu Item';
                document.getElementById('menu-form-cancel-btn').classList.add('hidden');

                // Re-enable category select
                const categorySelect = document.getElementById('menu-category-select');
                const categoryHidden = document.getElementById('menu-category-id-hidden');
                if (categorySelect) {
                    categorySelect.disabled = false;
                    categorySelect.style.opacity = '1';
                    categorySelect.style.cursor = 'pointer';
                }
                if (categoryHidden) {
                    categoryHidden.value = '';
                }

                // Reset category info text
                const categoryInfo = document.getElementById('category-info-text');
                if (categoryInfo) {
                    categoryInfo.textContent =
                        '{{ count($usedCategoryIds ?? []) }} danh mục đã được sử dụng và không hiển thị trong danh sách';
                    categoryInfo.classList.remove('text-yellow-600');
                }

                // Reset form fields
                form.reset();
                document.getElementById('menu-icon-input').value = '';
                document.getElementById('menu-icon-preview').innerHTML = '';
                document.getElementById('menu-target-select').value = '_self';
                document.getElementById('menu-active-checkbox').checked = true;

                // Update submit button
                document.getElementById('menu-submit-text').textContent = 'Thêm vào menu';
            }

            function deleteMenu(menuId, menuName) {
                if (confirm('Bạn có chắc muốn xóa menu "' + menuName + '"?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/menus/' + menuId;

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
