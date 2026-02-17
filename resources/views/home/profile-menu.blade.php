<div class="relative flex items-center gap-3 px-3 pb-8">
    <button
        type="button"
        class="profile-menu-trigger"
        aria-haspopup="true"
        aria-expanded="false"
    >
        <img
            src="{{ auth()->user()?->profile_photo_url ?? asset('images/no-image.jpeg') }}"
            alt="プロフィール"
            class="h-16 w-16 rounded-full object-cover"
        />
    </button>
    <div
        class="profile-menu hidden"
        role="dialog"
        aria-modal="false"
        aria-label="プロフィールメニュー"
    >
        <div class="profile-menu-card">
            <div class="profile-menu-header">
                <img
                    src="{{ auth()->user()?->profile_photo_url ?? asset('images/no-image.jpeg') }}"
                    alt="プロフィール"
                    class="profile-menu-avatar"
                />
                <p class="profile-menu-name">
                    {{ auth()->user()?->name ?? 'ゲスト' }} 様
                </p>
            </div>
            <div class="profile-menu-actions">
                <a
                    href="{{ route('settings') }}"
                    class="profile-menu-button"
                >
                    設定
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="profile-menu-button">
                        ログアウト
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
