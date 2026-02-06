@php($context = $context ?? null)
<aside class="flex h-full w-64 flex-col rounded-lg bg-white p-6 shadow-sm">
    <nav class="flex flex-1 flex-col justify-between text-lg">
        <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100" href="{{ route('home') }}">
            <span>HOME</span>
        </a>
        @if ($context === 'home')
            <button id="plan-register-trigger" type="button"
                class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100">
                <span>計画登録</span>
            </button>
        @else
            <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100"
                href="{{ route('home', ['open' => 'plan-register']) }}">
                <span>計画登録</span>
            </a>
        @endif
        <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100"
            href="{{ route('study-progress') }}">
            <span>学習実績</span>
        </a>
        {{-- リスケジュール起動 --}}
        @if ($context === 'home')
            <button id="plan-reschedule-trigger" type="button"
                class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100">
                <span>リスケジュール</span>
            </button>
        @else
            <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100"
                href="{{ route('home', ['open' => 'reschedule']) }}">
                <span>リスケジュール</span>
            </a>
        @endif
        <hr class="my-2 h-px w-40 self-center border-0 bg-black" />
        @include('home.profile-menu')
    </nav>
</aside>
