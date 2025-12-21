<x-guest-layout>
    <x-slot name="title">{{ __('auth::auth.register') }}</x-slot>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('auth::auth.create_account') }}</h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('auth::auth.already_registered') }}
            <a href="{{ route('auth.login') }}" class="font-medium text-black hover:text-gray-700">
                {{ __('auth::auth.login') }}
            </a>
        </p>
    </div>

    <form method="POST" action="{{ route('auth.register.submit') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('auth::auth.name') }}
            </label>
            <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('name') border-red-300 @enderror"
                placeholder="John Doe">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('auth::auth.email') }}
            </label>
            <input id="email" name="email" type="email" autocomplete="email" required
                value="{{ old('email') }}"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('email') border-red-300 @enderror"
                placeholder="you@example.com">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('auth::auth.password') }}
            </label>
            <input id="password" name="password" type="password" autocomplete="new-password" required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black @error('password') border-red-300 @enderror"
                placeholder="••••••••">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('auth::auth.password_confirmation') }}
            </label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                placeholder="••••••••">
        </div>

        <!-- Terms -->
        <div class="flex items-start">
            <input id="terms" name="terms" type="checkbox" required
                class="h-4 w-4 mt-0.5 rounded border-gray-300 text-black focus:ring-black">
            <label for="terms" class="ml-2 block text-sm text-gray-700">
                {{ __('auth::auth.agree_terms') }}
                <a href="#"
                    class="font-medium text-black hover:text-gray-700">{{ __('auth::auth.terms_service') }}</a>
                {{ __('auth::auth.and') }}
                <a href="#"
                    class="font-medium text-black hover:text-gray-700">{{ __('auth::auth.privacy_policy') }}</a>
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-semibold text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors duration-150">
                {{ __('auth::auth.create_account') }}
            </button>
        </div>

        <!-- Divider -->
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Or continue with</span>
            </div>
        </div>

        <!-- Social Login -->
        <div class="grid grid-cols-2 gap-3">
            <button type="button"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z" />
                </svg>
                <span class="ml-2">Google</span>
            </button>
            <button type="button"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.164 6.839 9.489.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.464-1.11-1.464-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.831.092-.646.35-1.086.636-1.336-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0112 6.836c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.161 22 16.418 22 12c0-5.523-4.477-10-10-10z" />
                </svg>
                <span class="ml-2">GitHub</span>
            </button>
        </div>
    </form>
</x-guest-layout>
