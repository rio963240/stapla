@props([
    'context' => 'home',
    'isAdmin' => false,
])

@php($homeRoute = $isAdmin ? 'admin.home' : 'home')
@php($studyProgressRoute = $isAdmin ? 'admin.study-progress' : 'study-progress')

<div
    x-data="{ drawerOpen: false }"
    class="flex h-[calc(100vh-3rem)] max-w-7xl mx-auto flex-col md:flex-row gap-0 md:gap-8 px-0 md:px-6 py-0 md:py-6 sm:px-6 lg:px-8 overflow-x-hidden"
>
    {{-- スマホのみ: ヘッダー（ロゴ + ハンバーガー） --}}
    <header class="md:hidden sticky top-0 z-30 flex h-14 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm">
        <a href="{{ route($homeRoute) }}" class="flex items-center gap-2">
            <x-application-mark class="block h-8 w-auto" />
            <span class="text-lg font-medium text-gray-800">スタプラ</span>
        </a>
        <button
            type="button"
            @click="drawerOpen = true"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange-500"
            aria-label="メニューを開く"
        >
            <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </header>

    {{-- スマホのみ: オーバーレイ（タップでドロワーを閉じる） --}}
    <div
        x-cloak
        x-show="drawerOpen"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="drawerOpen = false"
        class="fixed inset-0 z-40 bg-black/50 md:hidden"
        aria-hidden="true"
    ></div>

    {{-- PCのみ: 従来どおり常時表示のサイドバー（ドキュメントフロー内） --}}
    <aside class="hidden md:flex h-full w-64 flex-shrink-0 flex-col rounded-lg bg-white p-6 shadow-sm">
        @include('home.sidebar-nav', [
            'context' => $context,
            'isAdmin' => $isAdmin,
            'homeRoute' => $homeRoute,
            'studyProgressRoute' => $studyProgressRoute,
        ])
    </aside>

    {{-- スマホのみ: 左からスライドするドロワー --}}
    <aside
        @click="drawerOpen = false"
        :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed left-0 top-0 z-50 flex h-full w-64 max-w-[85vw] flex-col rounded-none bg-white shadow-xl transition-transform duration-200 ease-out md:hidden pt-[env(safe-area-inset-top)]"
    >
        <div class="flex items-center justify-between border-b border-gray-100 p-4">
            <span class="font-medium text-gray-800">メニュー</span>
            <button
                type="button"
                @click.stop="drawerOpen = false"
                class="p-2 rounded-md text-gray-500 hover:bg-gray-100"
                aria-label="メニューを閉じる"
            >
                <svg class="size-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="flex flex-1 flex-col overflow-y-auto p-4">
            @include('home.sidebar-nav', [
                'context' => $context,
                'isAdmin' => $isAdmin,
                'homeRoute' => $homeRoute,
                'studyProgressRoute' => $studyProgressRoute,
            ])
        </div>
    </aside>

    {{-- メインコンテンツ --}}
    <section class="min-h-0 min-w-0 flex-1 rounded-none md:rounded-lg bg-white p-4 md:p-6 shadow-none md:shadow-sm overflow-auto">
        {{ $slot }}
    </section>
</div>
