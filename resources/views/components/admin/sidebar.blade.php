@php
    use App\Services\MenuService;
    $menus = MenuService::all();
@endphp

<!-- Static sidebar for desktop -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6">
        <!-- Logo -->
        <div class="flex h-16 shrink-0 items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                {{ config('app.name') }}
            </h1>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
                        @foreach ($menus as $menu)
                            @php
                                $isActive = MenuService::isActive($menu);
                                $hasChildren = !empty($menu['children']);
                            @endphp

                            <li x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
                                @if ($hasChildren)
                                    <!-- Parent with children -->
                                    <button @click="open = !open"
                                        class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ $isActive ? 'bg-gray-50 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                        @if ($menu['icon'])
                                            <x-admin.icon :name="$menu['icon']" class="h-5 w-5 shrink-0" />
                                        @endif
                                        <span class="flex-1 text-left">{{ $menu['title'] }}</span>
                                        @if ($menu['badge'])
                                            <x-admin.badge :color="$menu['badge_color']">{{ $menu['badge'] }}</x-admin.badge>
                                        @endif
                                        <svg class="h-5 w-5 shrink-0 transition-transform" :class="open && 'rotate-90'"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <!-- Sub-menu -->
                                    <ul x-show="open" x-collapse class="mt-1 space-y-1 pl-9">
                                        @foreach ($menu['children'] as $child)
                                            @php $isChildActive = MenuService::isActive($child); @endphp
                                            <li>
                                                <a href="{{ $child['route'] ? route($child['route']) : $child['url'] }}"
                                                    class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 {{ $isChildActive ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                                    {{ $child['title'] }}
                                                    @if ($child['badge'])
                                                        <x-admin.badge
                                                            :color="$child['badge_color']">{{ $child['badge'] }}</x-admin.badge>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <!-- Simple link -->
                                    <a href="{{ $menu['route'] ? route($menu['route']) : $menu['url'] }}"
                                        class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ $isActive ? 'bg-gray-50 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                        @if ($menu['icon'])
                                            <x-admin.icon :name="$menu['icon']" class="h-5 w-5 shrink-0" />
                                        @endif
                                        {{ $menu['title'] }}
                                        @if ($menu['badge'])
                                            <x-admin.badge :color="$menu['badge_color']">{{ $menu['badge'] }}</x-admin.badge>
                                        @endif
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </li>

                <!-- Bottom section -->
                <li class="-mx-6 mt-auto">
                    <div
                        class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 border-t border-gray-200">
                        <div
                            class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="sr-only">Your profile</span>
                        <span aria-hidden="true">{{ auth()->user()->name ?? 'Admin' }}</span>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Mobile sidebar -->
<div x-data="{ open: false }" class="lg:hidden">
    <!-- Backdrop -->
    <div x-show="open" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-900/80" @click="open = false"></div>

    <!-- Sidebar panel -->
    <div x-show="open" x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 z-50 flex w-64 flex-col bg-white">
        <!-- Content same as desktop sidebar -->
        <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 px-6">
            <div class="flex h-16 shrink-0 items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ config('app.name') }}
                </h1>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <!-- Same nav as desktop -->
        </div>
    </div>
</div>
