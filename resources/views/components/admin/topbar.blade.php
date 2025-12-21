<div
    class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
    <!-- Mobile menu button -->
    <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="$dispatch('toggle-sidebar')">
        <span class="sr-only">Open sidebar</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <!-- Separator -->
    <div class="h-6 w-px bg-gray-200 lg:hidden"></div>

    <!-- Breadcrumb / Search -->
    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
        <div class="flex flex-1 items-center">
            @if (isset($breadcrumbs))
                {{ $breadcrumbs }}
            @else
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $title ?? 'Quản trị' }}
                </h2>
            @endif
        </div>
    </div>

    <!-- Right side -->
    <div class="flex items-center gap-x-4 lg:gap-x-6">
        <!-- Notifications -->
        <div x-data="{ 
            open: false, 
            notifications: [], 
            unreadCount: 0,
            loading: false,
            async loadNotifications() {
                this.loading = true;
                try {
                    const response = await fetch('{{ route('admin.notifications') }}?limit=10');
                    const data = await response.json();
                    this.notifications = data.data || [];
                    this.unreadCount = data.unread_count || 0;
                } catch (error) {
                    console.error('Failed to load notifications:', error);
                } finally {
                    this.loading = false;
                }
            },
            async markAsRead(id) {
                try {
                    const url = `{{ url('admin/notifications') }}/${id}/read`;
                    await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Content-Type': 'application/json',
                        },
                    });
                    this.loadNotifications();
                } catch (error) {
                    console.error('Failed to mark notification as read:', error);
                }
            },
            async markAllAsRead() {
                try {
                    await fetch('{{ route('admin.notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Content-Type': 'application/json',
                        },
                    });
                    this.loadNotifications();
                } catch (error) {
                    console.error('Failed to mark all as read:', error);
                }
            }
        }" 
        x-init="loadNotifications(); setInterval(() => loadNotifications(), 60000)" 
        @click.away="open = false" 
        class="relative">
            <button @click="open = !open; if(open) loadNotifications()" type="button" class="relative -m-2.5 p-2.5 text-gray-400 hover:text-gray-500 transition-colors">
                <span class="sr-only">View notifications</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <span x-show="unreadCount > 0" x-text="unreadCount" 
                    class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-semibold text-white bg-red-600 rounded-full border-2 border-white"></span>
            </button>

            <!-- Notifications dropdown -->
            <div x-show="open" 
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                class="absolute right-0 z-50 mt-2 w-96 origin-top-right rounded-lg bg-white shadow-xl border border-gray-200 divide-y divide-gray-100">
                <!-- Header -->
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Thông báo</h3>
                        <button @click="markAllAsRead()" x-show="unreadCount > 0" 
                            class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                            Đánh dấu tất cả đã đọc
                        </button>
                    </div>
                </div>
                
                <!-- Notifications list -->
                <div class="max-h-96 overflow-y-auto">
                    <template x-if="loading">
                        <div class="p-6 text-center">
                            <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-gray-900"></div>
                            <p class="mt-2 text-sm text-gray-500">Đang tải...</p>
                        </div>
                    </template>
                    <template x-if="!loading && notifications.length === 0">
                        <div class="p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Không có thông báo</p>
                        </div>
                    </template>
                    <template x-for="notification in notifications" :key="notification.id">
                        <a :href="notification.link || '#'" 
                            @click.prevent="if(!notification.is_read) markAsRead(notification.id); window.location.href = notification.link || '#'"
                            class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0"
                            :class="{'bg-blue-50/50': !notification.is_read}">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-2 h-2 rounded-full" :class="notification.is_read ? 'bg-gray-300' : 'bg-blue-600'"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900" x-text="notification.title"></p>
                                    <p class="mt-1 text-sm text-gray-600 leading-relaxed" x-text="notification.message"></p>
                                    <p class="mt-1.5 text-xs text-gray-400" x-text="new Date(notification.created_at).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })"></p>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
                
                <!-- Footer -->
                <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-200 rounded-b-lg text-center">
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                        Xem tất cả
                    </a>
                </div>
            </div>
        </div>

        <!-- Separator -->
        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200"></div>

        <!-- Profile dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" type="button" class="-m-1.5 flex items-center p-1.5">
                <span class="sr-only">Open user menu</span>
                <div
                    class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <span class="hidden lg:flex lg:items-center">
                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </span>
                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            </button>

            <!-- Dropdown menu -->
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5">
                <a href="#" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Hồ sơ</a>
                <a href="#" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Cài đặt</a>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit"
                        class="block w-full text-left px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
