@php($context = $context ?? null)
@php($isAdmin = $isAdmin ?? false)
@php($homeRoute = $isAdmin ? 'admin.home' : 'home')
@php($studyProgressRoute = $isAdmin ? 'admin.study-progress' : 'study-progress')
<aside class="flex h-full w-64 flex-col rounded-lg bg-white p-6 shadow-sm">
    @include('home.sidebar-nav', [
        'context' => $context,
        'isAdmin' => $isAdmin,
        'homeRoute' => $homeRoute,
        'studyProgressRoute' => $studyProgressRoute,
    ])
</aside>
