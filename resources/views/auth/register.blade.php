<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-500">
                <img src="{{ asset('images/stapla-logo.png') }}" alt="スタプラ" class="h-16 w-16" />
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('名前') }}" />
                <x-input id="name" class="mt-1 block w-full border-orange-300 px-4 py-2.5 text-base focus:border-orange-400 focus:ring-orange-400" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('メールアドレス') }}" />
                <x-input id="email" class="mt-1 block w-full border-orange-300 px-4 py-2.5 text-base focus:border-orange-400 focus:ring-orange-400" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('パスワード') }}" />
                <p class="mt-1 text-sm text-gray-600">半角英数字を含む8文字以上16文字以下で入力してください。</p>
                <x-input id="password" class="mt-1 block w-full border-orange-300 px-4 py-2.5 text-base focus:border-orange-400 focus:ring-orange-400" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('確認用パスワード') }}" />
                <x-input id="password_confirmation" class="mt-1 block w-full border-orange-300 px-4 py-2.5 text-base focus:border-orange-400 focus:ring-orange-400" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" class="text-orange-500 focus:ring-orange-400" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400" href="{{ route('login') }}">
                    {{ __('すでに登録済みの方はこちら') }}
                </a>

                <x-button class="ms-4 bg-orange-500 hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700 focus:ring-orange-400">
                    {{ __('新規登録') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
