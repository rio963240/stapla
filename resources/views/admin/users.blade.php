<x-app-layout :show-navigation="false">
    @php($isAdmin = true)
    <div class="h-screen bg-gray-100 overflow-hidden">
        <div class="mx-auto flex h-[calc(100vh-3rem)] max-w-7xl gap-8 px-6 py-6 sm:px-6 lg:px-8">
            @include('home.sidebar', ['context' => 'admin', 'isAdmin' => $isAdmin])

            <section class="flex-1 rounded-lg bg-white p-6 shadow-sm overflow-auto">
                <h1 class="text-xl font-semibold text-gray-800">ユーザー管理</h1>
                <p class="mt-2 text-sm text-gray-600">管理機能の画面をここに追加予定です。</p>
            </section>
        </div>
    </div>
</x-app-layout>
