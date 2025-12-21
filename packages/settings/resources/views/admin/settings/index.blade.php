<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('settings.title') }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ __('settings.subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <!-- Group Tabs -->
        <x-admin.card class="mb-6 p-0">
            <div class="bg-white rounded-lg">
                <nav class="flex space-x-8 overflow-x-auto px-6 py-0 gap-4" aria-label="Tabs">
                    @foreach ($groups as $groupName)
                        <a href="{{ route('admin.settings.index', ['group' => $groupName]) }}"
                            class="whitespace-nowrap py-4 px-1 text-sm transition-colors duration-200 {{ $group === $groupName ? 'text-black font-bold border-b-2 border-black' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
                            {{ $groupNames[$groupName] ?? ucfirst($groupName) }}
                        </a>
                    @endforeach
                </nav>
                <div class="border-t border-gray-200"></div>
            </div>
        </x-admin.card>

        <!-- Settings Form -->
        <x-admin.card>
            <div class="space-y-6">
                @forelse ($settings as $setting)
                    @php
                        $fieldTranslationKey = 'settings.fields.' . $setting->key;
                        $translatedLabel = __($fieldTranslationKey);
                        if ($translatedLabel === $fieldTranslationKey) {
                            $translatedLabel = $setting->description ?: $setting->key;
                        }
                    @endphp

                    <div>
                        <label for="setting_{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $translatedLabel }}
                            <span class="text-xs text-gray-400 ml-2">({{ $setting->key }})</span>
                        </label>

                        @if ($setting->type === 'text' || $setting->type === 'email')
                            <input type="{{ $setting->type }}" name="settings[{{ $setting->key }}]"
                                id="setting_{{ $setting->key }}"
                                value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                        @elseif ($setting->type === 'textarea')
                            <textarea name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">{{ old('settings.' . $setting->key, $setting->value) }}</textarea>
                        @elseif ($setting->type === 'number')
                            <input type="number" name="settings[{{ $setting->key }}]"
                                id="setting_{{ $setting->key }}"
                                value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                        @elseif ($setting->type === 'boolean')
                            <select name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                                <option value="1"
                                    {{ old('settings.' . $setting->key, $setting->value) == '1' ? 'selected' : '' }}>
                                    {{ __('common.yes') }}</option>
                                <option value="0"
                                    {{ old('settings.' . $setting->key, $setting->value) == '0' ? 'selected' : '' }}>
                                    {{ __('common.no') }}</option>
                            </select>
                        @else
                            <input type="text" name="settings[{{ $setting->key }}]"
                                id="setting_{{ $setting->key }}"
                                value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                        @endif

                        @error('settings.' . $setting->key)
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">{{ __('settings.messages.empty') }}</p>
                @endforelse
            </div>

            @if ($settings->count() > 0)
                <div class="mt-6 flex justify-end">
                    <x-admin.button type="submit">{{ __('settings.actions.save') }}</x-admin.button>
                </div>
            @endif
        </x-admin.card>
    </form>
</x-admin-layout>
