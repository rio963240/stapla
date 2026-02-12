<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-500">
                <img src="{{ asset('images/stapla-logo.png') }}" alt="スタプラ" class="h-16 w-16" />
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <x-label for="email" value="{{ __('メールアドレス') }}" />
                <x-input id="email" class="mt-1 block w-full border-orange-300 px-4 py-2.5 text-base focus:border-orange-400 focus:ring-orange-400" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('パスワード') }}" />
                <x-input id="password" class="mt-1 block w-full border-orange-300 px-4 py-2.5 text-base focus:border-orange-400 focus:ring-orange-400" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('確認用パスワード') }}" />
                <x-input id="password_confirmation" class="mt-1 block w-full border-orange-300 px-4 py-2.5 text-base focus:border-orange-400 focus:ring-orange-400" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="bg-orange-500 hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700 focus:ring-orange-400">
                    {{ __('パスワード再設定') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
