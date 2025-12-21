@props(['type' => 'info', 'message'])

@php
    $config = [
        'success' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-400',
            'text' => 'text-green-800',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-400',
            'text' => 'text-red-800',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />',
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-400',
            'text' => 'text-yellow-800',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-400',
            'text' => 'text-blue-800',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />',
        ],
    ];

    $c = $config[$type] ?? $config['info'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-md border-l-4 p-4 mb-4 {$c['bg']} {$c['border']}"]) }}>
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 {{ $c['text'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                {!! $c['icon'] !!}
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm {{ $c['text'] }}">
                {{ $message ?? $slot }}
            </p>
        </div>
    </div>
</div>
