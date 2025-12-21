<div class="menu-item {{ !$menu->is_active ? 'inactive' : '' }}" data-id="{{ $menu->id }}">
    <div class="menu-item-header">
        <div class="menu-item-title">
            <svg class="menu-item-handle w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
            </svg>
            <div class="menu-item-info">
                <div class="menu-item-name flex items-center gap-2">
                    @if ($menu->icon)
                        @php
                            $iconUrl = $menu->icon;
                            if (!str_starts_with($iconUrl, 'http') && !str_starts_with($iconUrl, '/')) {
                                $iconUrl = asset('storage/' . ltrim($iconUrl, '/'));
                            } elseif (!str_starts_with($iconUrl, 'http')) {
                                $iconUrl = asset($iconUrl);
                            }
                        @endphp
                        <img src="{{ $iconUrl }}" alt="Icon" class="w-5 h-5 object-cover rounded">
                    @endif
                    <span class="font-semibold text-gray-900">{{ $menu->display_name }}</span>
                    @if (!$menu->is_active)
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Đã
                            tắt</span>
                    @endif
                </div>
                <div class="menu-item-type flex items-center gap-2 mt-1">
                    @if ($menu->type === 'category')
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-cyan-100 text-cyan-800">Danh
                            mục</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="menu-item-actions">
            <button type="button" class="btn-edit"
                onclick="editMenu({{ $menu->id }}, {{ json_encode([
                    'type' => $menu->type,
                    'name' => $menu->name,
                    'url' => $menu->url,
                    'icon' => $menu->icon,
                    'target' => $menu->target,
                    'is_active' => $menu->is_active,
                    'category_id' => $menu->category_id,
                ]) }})">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
            <button type="button" class="btn-delete"
                onclick="deleteMenu({{ $menu->id }}, '{{ addslashes($menu->display_name) }}')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>

    @if ($menu->children && $menu->children->count() > 0)
        <div class="menu-children">
            @foreach ($menu->children as $child)
                @include('settings::admin.menus.partials.menu-item-sortable', [
                    'menu' => $child,
                    'level' => $level + 1,
                ])
            @endforeach
        </div>
    @endif
</div>
