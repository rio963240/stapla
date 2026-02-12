<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-500">
                <img src="{{ asset('images/stapla-logo.png') }}" alt="スタプラ" class="h-16 w-16" />
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 text-sm font-medium text-green-600">
                {{ __($value) }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <x-label for="email" value="メールアドレス" />
                <x-input
                    id="email"
                    class="mt-1 block w-full border-orange-300 px-4 py-2.5 text-base focus:border-orange-400 focus:ring-orange-400"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                />
            </div>

            <div>
                <x-label for="password" value="パスワード" />
                <x-input
                    id="password"
                    class="mt-1 block w-full border-orange-300 px-4 py-2.5 text-base focus:border-orange-400 focus:ring-orange-400"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                />
            </div>

            <div>
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" class="text-orange-500 focus:ring-orange-400" />
                    <span class="ms-2 text-sm text-gray-600">ログイン状態を保持する</span>
                </label>
            </div>

            <div class="flex flex-col items-end gap-3">
                @if (Route::has('register'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400" href="{{ route('register') }}">
                        新規登録はこちら
                    </a>
                @endif

                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400" href="{{ route('password.request') }}">
                        パスワードを忘れた方はこちら
                    </a>
                @endif

                <x-button class="self-center w-32 justify-center bg-orange-500 hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700 focus:ring-orange-400">
                    ログイン
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
