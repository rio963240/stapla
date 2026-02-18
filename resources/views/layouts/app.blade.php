@props([
    'showNavigation' => true,
])
@php
    // サイドバーレイアウトを使うページ: PCでは横いっぱいのトップナビを表示、スマホでは出さない（ドロワーのみ）
    $useSidebarLayout = request()->routeIs('home', 'study-progress', 'settings', 'admin.*');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        @stack('styles')
    </head>
    <body class="font-sans antialiased {{ $useSidebarLayout ? 'sidebar-layout-page' : '' }}">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @if ($useSidebarLayout)
                {{-- 共通ヘッダー（PC・スマホとも表示、スマホではハンバーガーでドロワーを開く） --}}
                @livewire('navigation-menu')
            @elseif ($showNavigation)
                @livewire('navigation-menu')
            @endif

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @stack('scripts')
        @livewireScripts
    </body>
</html>
