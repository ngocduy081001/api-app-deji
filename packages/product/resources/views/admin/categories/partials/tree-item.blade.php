@php
    $isCurrent = isset($currentId) && $currentId == $category->id;
@endphp
<li class="py-1 draggable-item {{ $isCurrent ? 'bg-blue-50 border-l-2 border-blue-500' : '' }}"
    data-id="{{ $category->id }}" data-parent-id="{{ $category->parent_id ?? '' }}"
    data-sort-order="{{ $category->sort_order }}">
    <div class="flex items-center text-sm hover:bg-gray-50 rounded px-2 py-1 -mx-2 -my-1 group"
        style="padding-left: {{ $level * 20 }}px;">
        <svg class="w-4 h-4 text-gray-400 mr-1 drag-handle cursor-move" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
        </svg>
        @if ($category->children->count() > 0)
            <button type="button" class="tree-toggle mr-1 text-gray-400 hover:text-gray-600 focus:outline-none"
                data-target="tree-children-{{ $category->id }}">
                <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        @else
            <span class="w-5 inline-block"></span>
        @endif
        <a href="#"
            class="category-edit-link text-gray-700 flex-1 hover:text-black {{ $isCurrent ? 'font-semibold text-blue-700' : '' }}"
            data-id="{{ $category->id }}">
            {{ $category->name }}
        </a>
        @if ($category->is_featured)
            <span
                class="ml-2 inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                Nổi bật
            </span>
        @endif
        @if (!$category->is_active)
            <span class="ml-2 text-xs text-gray-400">(Ẩn)</span>
        @endif
        <div class="ml-2 opacity-0 group-hover:opacity-100 flex items-center space-x-1">
            <a href="#" class="category-edit-link text-blue-600 hover:text-blue-800" data-id="{{ $category->id }}"
                title="Chỉnh sửa">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
            <button type="button" onclick="deleteCategory({{ $category->id }})"
                class="text-red-600 hover:text-red-800" title="Xóa">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
    @if ($category->children->count() > 0)
        <ul id="tree-children-{{ $category->id }}" class="hidden mt-1 sortable-children"
            data-parent-id="{{ $category->id }}">
            @foreach ($category->children as $child)
                @include('product::admin.categories.partials.tree-item', [
                    'category' => $child,
                    'level' => $level + 1,
                    'currentId' => $currentId ?? null,
                ])
            @endforeach
        </ul>
    @endif
</li>
