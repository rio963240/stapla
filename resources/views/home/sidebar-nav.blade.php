@php($context = $context ?? null)
@php($isAdmin = $isAdmin ?? false)
@php($homeRoute = $homeRoute ?? ($isAdmin ? 'admin.home' : 'home'))
@php($studyProgressRoute = $studyProgressRoute ?? ($isAdmin ? 'admin.study-progress' : 'study-progress'))
<nav class="flex flex-1 flex-col justify-between text-lg">
    <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100" href="{{ route($homeRoute) }}">
        <span>HOME</span>
    </a>
    @if ($context === 'home')
        <button type="button"
            class="plan-register-trigger flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 w-full text-left">
            <span>計画登録</span>
        </button>
    @else
        <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100"
            href="{{ route($homeRoute, ['open' => 'plan-register']) }}">
            <span>計画登録</span>
        </a>
    @endif
    <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100"
        href="{{ route($studyProgressRoute) }}">
        <span>学習実績</span>
    </a>
    {{-- リスケジュール起動 --}}
    @if ($context === 'home')
        <button type="button"
            class="plan-reschedule-trigger flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 w-full text-left">
            <span>リスケジュール</span>
        </button>
    @else
        <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100"
            href="{{ route($homeRoute, ['open' => 'reschedule']) }}">
            <span>リスケジュール</span>
        </a>
    @endif
    <hr class="my-2 h-px w-40 self-center border-0 bg-black" />
    @if ($isAdmin)
        <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100"
            href="{{ route('admin.qualifications') }}">
            <span>資格情報管理</span>
        </a>
        <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100"
            href="{{ route('admin.backups') }}">
            <span>バックアップ</span>
        </a>
        <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100"
            href="{{ route('admin.users') }}">
            <span>ユーザー管理</span>
        </a>
        <hr class="my-2 h-px w-40 self-center border-0 bg-black" />
    @endif
    @include('home.profile-menu')
</nav>
