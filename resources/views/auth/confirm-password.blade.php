<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-500">
                <img src="{{ asset('images/stapla-logo.png') }}" alt="スタプラ" class="h-16 w-16" />
            </div>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="mt-1 block w-full border-orange-300 px-4 py-2.5 text-base focus:border-orange-400 focus:ring-orange-400" type="password" name="password" required autocomplete="current-password" autofocus />
            </div>

            <div class="flex justify-end mt-4">
                <x-button class="ms-4 bg-orange-500 hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700 focus:ring-orange-400">
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
