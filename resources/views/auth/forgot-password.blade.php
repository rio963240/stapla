<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-500">
                <img src="{{ asset('images/stapla-logo.png') }}" alt="スタプラ" class="h-16 w-16" />
            </div>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600 leading-relaxed">
            {{ __('パスワードをお忘れですか？') }}<br>
            <span class="block mt-1">
                {{ __('登録メールアドレス宛に、パスワードリセット用のURLを送信します') }}
            </span>
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __($value) }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('メールアドレス') }}" />
                <x-input id="email" class="mt-1 block w-full border-orange-300 px-4 py-2.5 text-base focus:border-orange-400 focus:ring-orange-400" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="bg-orange-500 hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700 focus:ring-orange-400">
                    {{ __('メールを送信') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
