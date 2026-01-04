<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Quản lý Tìm kiếm Phổ biến
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Quản lý các từ khóa tìm kiếm hiển thị trên ứng dụng
                </p>
            </div>

            <button type="button" onclick="toggleCreateForm()"
                class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm từ khóa
            </button>
        </div>
    </x-slot>

    {{-- CREATE FORM --}}
    <x-admin.card id="createForm" class="mb-6 hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Thêm từ khóa mới</h3>
            <button type="button" onclick="toggleCreateForm()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="createSearchForm" onsubmit="saveSearch(event, null)" class="space-y-4">
            <div id="createErrorMessage"
                class="hidden mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="createKeyword" class="block text-sm font-medium text-gray-700 mb-2">
                        Từ khóa <span class="text-red-500">*</span>
                    </label>
                    <input id="createKeyword" name="keyword" type="text" required
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent"
                        placeholder="VD: iPhone 15">
                </div>

                <div>
                    <label for="createSortOrder" class="block text-sm font-medium text-gray-700 mb-2">
                        Thứ tự
                    </label>
                    <input id="createSortOrder" name="sort_order" type="number" min="0" value="0"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input id="createIsActive" name="is_active" type="checkbox" checked
                        class="w-4 h-4 rounded border-gray-300 text-black focus:ring-2 focus:ring-black cursor-pointer">
                    <span class="text-sm text-gray-700">Kích hoạt</span>
                </label>

                <div class="flex gap-3">
                    <button type="button" onclick="toggleCreateForm()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100">
                        Hủy
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 disabled:opacity-50">
                        <span id="createSubmitText">Lưu</span>
                        <span id="createSubmitLoading" class="hidden">Đang lưu...</span>
                    </button>
                </div>
            </div>
        </form>
    </x-admin.card>

    {{-- FILTER --}}
    <x-admin.card class="mb-6">
        <form method="GET" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full rounded-md border-gray-300 focus:border-black focus:ring-black"
                    placeholder="Nhập từ khóa...">
            </div>

            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="status" class="w-full rounded-md border-gray-300 focus:border-black focus:ring-black">
                    <option value="">Tất cả</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tắt</option>
                </select>
            </div>

            <x-admin.button type="submit">Lọc</x-admin.button>

            @if (request('search') || request('status'))
                <a href="{{ route('admin.popular-searches.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                    Xóa bộ lọc
                </a>
            @endif
        </form>
    </x-admin.card>

    {{-- TABLE --}}
    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Từ khóa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thứ tự</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lượt click</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($popularSearches as $search)
                        {{-- Display Row --}}
                        <tr id="row-{{ $search->id }}" class="view-mode">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $search->keyword }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $search->sort_order }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ number_format($search->click_count) }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 text-xs rounded-full
                                    {{ $search->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $search->is_active ? 'Hoạt động' : 'Tắt' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="toggleEditForm({{ $search->id }})"
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                    Sửa
                                </button>
                                <button
                                    onclick="deleteSearch({{ $search->id }}, {{ json_encode($search->keyword) }})"
                                    class="text-red-600 hover:text-red-900">
                                    Xóa
                                </button>
                            </td>
                        </tr>

                        {{-- Edit Form Row --}}
                        <tr id="edit-row-{{ $search->id }}" class="edit-mode hidden">
                            <td colspan="5" class="px-6 py-4 bg-gray-50">
                                <form id="editForm-{{ $search->id }}"
                                    onsubmit="saveSearch(event, {{ $search->id }})" class="space-y-4">
                                    <div id="editErrorMessage-{{ $search->id }}"
                                        class="hidden mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Từ khóa <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="keyword" required
                                                value="{{ $search->keyword }}"
                                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent"
                                                placeholder="VD: iPhone 15">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Thứ tự
                                            </label>
                                            <input type="number" name="sort_order" min="0"
                                                value="{{ $search->sort_order }}"
                                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="is_active"
                                                {{ $search->is_active ? 'checked' : '' }}
                                                class="w-4 h-4 rounded border-gray-300 text-black focus:ring-2 focus:ring-black cursor-pointer">
                                            <span class="text-sm text-gray-700">Kích hoạt</span>
                                        </label>

                                        <div class="flex gap-3">
                                            <button type="button" onclick="toggleEditForm({{ $search->id }})"
                                                class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100">
                                                Hủy
                                            </button>
                                            <button type="submit"
                                                class="px-5 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 disabled:opacity-50">
                                                <span id="editSubmitText-{{ $search->id }}">Lưu</span>
                                                <span id="editSubmitLoading-{{ $search->id }}" class="hidden">Đang
                                                    lưu...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Chưa có từ khóa nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($popularSearches->hasPages())
            <div class="mt-4">
                {{ $popularSearches->links() }}
            </div>
        @endif
    </x-admin.card>

    {{-- SCRIPT --}}
    @push('scripts')
        <script>
            /**
             * Toggle create form visibility
             */
            function toggleCreateForm() {
                const form = document.getElementById('createForm');
                form.classList.toggle('hidden');

                if (!form.classList.contains('hidden')) {
                    // Reset form when showing
                    document.getElementById('createSearchForm').reset();
                    document.getElementById('createSortOrder').value = 0;
                    document.getElementById('createIsActive').checked = true;
                    hideCreateError();
                    // Focus on keyword input
                    setTimeout(() => document.getElementById('createKeyword').focus(), 100);
                }
            }

            /**
             * Toggle edit form for a specific row
             */
            function toggleEditForm(id) {
                const viewRow = document.getElementById('row-' + id);
                const editRow = document.getElementById('edit-row-' + id);

                // Hide all other edit forms first
                document.querySelectorAll('.edit-mode').forEach(row => {
                    if (row.id !== 'edit-row-' + id) {
                        row.classList.add('hidden');
                        const otherId = row.id.replace('edit-row-', '');
                        document.getElementById('row-' + otherId).classList.remove('hidden');
                    }
                });

                // Toggle current row
                if (editRow.classList.contains('hidden')) {
                    viewRow.classList.add('hidden');
                    editRow.classList.remove('hidden');
                    hideEditError(id);
                    // Focus on first input
                    setTimeout(() => {
                        const firstInput = editRow.querySelector('input[type="text"]');
                        if (firstInput) firstInput.focus();
                    }, 100);
                } else {
                    viewRow.classList.remove('hidden');
                    editRow.classList.add('hidden');
                }
            }

            /**
             * Show error in create form
             */
            function showCreateError(msg) {
                const el = document.getElementById('createErrorMessage');
                el.textContent = msg;
                el.classList.remove('hidden');
            }

            /**
             * Hide error in create form
             */
            function hideCreateError() {
                document.getElementById('createErrorMessage').classList.add('hidden');
            }

            /**
             * Show error in edit form
             */
            function showEditError(id, msg) {
                const el = document.getElementById('editErrorMessage-' + id);
                el.textContent = msg;
                el.classList.remove('hidden');
            }

            /**
             * Hide error in edit form
             */
            function hideEditError(id) {
                document.getElementById('editErrorMessage-' + id).classList.add('hidden');
            }

            /**
             * Save search keyword (create or update)
             */
            function saveSearch(e, editId) {
                e.preventDefault();

                const isEdit = editId !== null;
                const form = isEdit ?
                    document.getElementById('editForm-' + editId) :
                    document.getElementById('createSearchForm');

                const formData = new FormData(form);
                const keyword = formData.get('keyword')?.trim();

                if (!keyword) {
                    if (isEdit) {
                        showEditError(editId, 'Vui lòng nhập từ khóa');
                    } else {
                        showCreateError('Vui lòng nhập từ khóa');
                    }
                    return;
                }

                // Hide errors
                if (isEdit) hideEditError(editId);
                else hideCreateError();

                const payload = {
                    keyword: keyword,
                    sort_order: Number(formData.get('sort_order')) || 0,
                    is_active: formData.get('is_active') === 'on'
                };

                const url = isEdit ?
                    `/admin/popular-searches/${editId}` :
                    `/admin/popular-searches`;

                // Disable submit button
                const submitButton = isEdit ?
                    form.querySelector('button[type="submit"]') :
                    form.querySelector('button[type="submit"]');
                const submitText = isEdit ?
                    document.getElementById('editSubmitText-' + editId) :
                    document.getElementById('createSubmitText');
                const submitLoading = isEdit ?
                    document.getElementById('editSubmitLoading-' + editId) :
                    document.getElementById('createSubmitLoading');

                submitButton.disabled = true;
                if (submitText) submitText.classList.add('hidden');
                if (submitLoading) submitLoading.classList.remove('hidden');

                fetch(url, {
                        method: isEdit ? 'PUT' : 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            const errorMsg = data.message || 'Có lỗi xảy ra';
                            if (isEdit) {
                                showEditError(editId, errorMsg);
                            } else {
                                showCreateError(errorMsg);
                            }
                            // Re-enable submit button
                            submitButton.disabled = false;
                            if (submitText) submitText.classList.remove('hidden');
                            if (submitLoading) submitLoading.classList.add('hidden');
                        }
                    })
                    .catch(() => {
                        const errorMsg = 'Không thể lưu dữ liệu';
                        if (isEdit) {
                            showEditError(editId, errorMsg);
                        } else {
                            showCreateError(errorMsg);
                        }
                        // Re-enable submit button
                        submitButton.disabled = false;
                        if (submitText) submitText.classList.remove('hidden');
                        if (submitLoading) submitLoading.classList.add('hidden');
                    });
            }

            /**
             * Delete search keyword
             */
            function deleteSearch(id, keyword) {
                if (!confirm(`Bạn có chắc chắn muốn xóa từ khóa "${keyword}"?`)) {
                    return;
                }

                fetch(`/admin/popular-searches/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Có lỗi xảy ra');
                        }
                    })
                    .catch(() => {
                        alert('Có lỗi xảy ra');
                    });
            }
        </script>
    @endpush
</x-admin-layout>
