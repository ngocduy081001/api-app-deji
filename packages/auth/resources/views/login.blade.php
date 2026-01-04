<x-guest-layout>
    <x-slot name="title">{{ __('auth::auth.login') }}</x-slot>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('auth::auth.sign_in_account') }}</h2>
        <p class="mt-2 text-sm text-gray-600">
            {{-- {{ __('auth::auth.not_registered') }} --}}
            {{-- <a href="{{ route('auth.register') }}" class="font-medium text-black hover:text-gray-700">
                {{ __('auth::auth.create_account') }}
            </a> --}}
        </p>
    </div>

    <form method="POST" action="{{ route('auth.login.submit') }}" class="space-y-6">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('auth::auth.email') }}
            </label>
            <input value="admin@eyedesignsydney.com.au" tabindex="1" autocomplete="off" id="email" name="email"
                type="email" autocomplete="email" required value="{{ old('email') }}"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('email') border-red-300 @enderror"
                placeholder="you@example.com">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-sm font-medium text-gray-700">
                    {{ __('auth::auth.password') }}
                </label>
                <a href="#" class="text-sm font-medium text-black hover:text-gray-700">
                    {{ __('auth::auth.forgot_password') }}
                </a>
            </div>
            <input tabindex="2" autocomplete="off" value="N!him0108@#$%*" id="password" name="password"
                type="password" autocomplete="current-password" required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('password') border-red-300 @enderror"
                placeholder="••••••••">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember" name="remember" type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black">
            <label for="remember" class="ml-2 block text-sm text-gray-700">
                {{ __('auth::auth.remember_me') }}
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-semibold text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors duration-150">
                {{ __('auth::auth.login') }}
            </button>
        </div>
    </form>`
</x-guest-layout>
