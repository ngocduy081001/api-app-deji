@props([
    'model',
    'routePrefix', // e.g., 'admin.articles', 'admin.customers'
    'showView' => true,
    'showEdit' => true,
    'showDelete' => true,
    'deleteMessage' => 'Bạn có chắc chắn muốn xóa?',
    'viewTitle' => 'Xem chi tiết',
    'editTitle' => 'Chỉnh sửa',
    'deleteTitle' => 'Xóa',
])

<div class="flex items-center justify-end gap-2">
    @if ($showView)
        <a href="{{ route($routePrefix . '.show', $model) }}"
            class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-md transition-colors"
            title="{{ $viewTitle }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </a>
    @endif

    @if ($showEdit)
        <a href="{{ route($routePrefix . '.edit', $model) }}"
            class="p-2 text-black hover:text-gray-700 hover:bg-gray-100 rounded-md transition-colors"
            title="{{ $editTitle }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </a>
    @endif

    @if ($showDelete)
        <form method="POST" action="{{ route($routePrefix . '.destroy', $model) }}"
            onsubmit="return confirm('{{ $deleteMessage }}')" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors"
                title="{{ $deleteTitle }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </form>
    @endif
</div>
