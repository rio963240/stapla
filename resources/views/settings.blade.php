<x-app-layout>
    @push('styles')
        @vite('resources/css/settings.css')
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            設定
        </h2>
    </x-slot>

    <div class="settings-page">
        <div class="settings-container">
            <section class="settings-card">
                <div class="settings-card-header">
                    <h3 class="settings-card-title">基本情報</h3>
                    <button
                        type="button"
                        class="settings-save-button"
                        data-toast-status="success"
                        data-toast-message="基本情報を保存しました"
                    >
                        保存
                    </button>
                </div>
                <div class="settings-card-body">
                    <div class="settings-avatar">
                        <img
                            src="{{ auth()->user()?->profile_photo_url ?? asset('images/no-image.jpeg') }}"
                            alt="プロフィール"
                            class="settings-avatar-image"
                        />
                        <div class="settings-avatar-actions">
                            <button type="button" class="settings-secondary-button">
                                アイコン変更
                            </button>
                            {{-- <p class="settings-muted">JPEG/PNG 5MB まで</p> --}}
                        </div>
                    </div>

                    <div class="settings-grid">
                        <label class="settings-label" for="settings-name">名前</label>
                        <input
                            id="settings-name"
                            type="text"
                            class="settings-input"
                            value="{{ auth()->user()?->name ?? '' }}"
                        />

                        <label class="settings-label" for="settings-email">E-mail</label>
                        <input
                            id="settings-email"
                            type="email"
                            class="settings-input settings-input-disabled"
                            value="{{ auth()->user()?->email ?? '' }}"
                            disabled
                        />
                    </div>
                </div>
            </section>

            <section class="settings-card">
                <div class="settings-card-header">
                    <h3 class="settings-card-title">通知設定</h3>
                    <button
                        type="button"
                        class="settings-save-button"
                        data-toast-status="success"
                        data-toast-message="通知設定を保存しました"
                    >
                        保存
                    </button>
                </div>
                <div class="settings-card-body settings-notify">
                    <div class="settings-qr-block">
                        <p class="settings-label">LINE QRコード</p>
                        <img
                            src="{{ asset('images/line-qr-dummy.svg') }}"
                            alt="LINE QRコード"
                            class="settings-qr-image"
                        />
                        <p class="settings-muted">LINEで友だち追加して通知を受け取ります</p>
                    </div>

                    <div class="settings-grid">
                        <label class="settings-label" for="line-notify">LINE通知</label>
                        <label class="settings-switch">
                            <input id="line-notify" type="checkbox" checked data-time-toggle="line" />
                            <span class="settings-slider"></span>
                        </label>

                        <div class="settings-time-block" data-time-block="line">
                            <p class="settings-label">LINE通知時間</p>
                            <div class="settings-time-selects">
                                <div class="settings-time-select">
                                    <span class="settings-time-tag">朝</span>
                                    <select class="settings-select" aria-label="LINE通知 朝">
                                        @for ($hour = 0; $hour < 24; $hour++)
                                            @php($time = sprintf('%02d:00', $hour))
                                            <option value="{{ $time }}" @selected($time === '08:00')>{{ $time }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="settings-time-select">
                                    <span class="settings-time-tag">夜</span>
                                    <select class="settings-select" aria-label="LINE通知 夜">
                                        @for ($hour = 0; $hour < 24; $hour++)
                                            @php($time = sprintf('%02d:00', $hour))
                                            <option value="{{ $time }}" @selected($time === '20:00')>{{ $time }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <label class="settings-label" for="mail-notify">メール通知</label>
                        <label class="settings-switch">
                            <input id="mail-notify" type="checkbox" data-time-toggle="mail" />
                            <span class="settings-slider"></span>
                        </label>

                        <div class="settings-time-block is-hidden" data-time-block="mail">
                            <p class="settings-label">メール通知時間</p>
                            <div class="settings-time-selects">
                                <div class="settings-time-select">
                                    <span class="settings-time-tag">朝</span>
                                    <select class="settings-select" aria-label="メール通知 朝">
                                        @for ($hour = 0; $hour < 24; $hour++)
                                            @php($time = sprintf('%02d:00', $hour))
                                            <option value="{{ $time }}" @selected($time === '08:00')>{{ $time }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="settings-time-select">
                                    <span class="settings-time-tag">夜</span>
                                    <select class="settings-select" aria-label="メール通知 夜">
                                        @for ($hour = 0; $hour < 24; $hour++)
                                            @php($time = sprintf('%02d:00', $hour))
                                            <option value="{{ $time }}" @selected($time === '20:00')>{{ $time }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="settings-card">
                <div class="settings-card-header">
                    <h3 class="settings-card-title">パスワード変更</h3>
                    <button
                        type="button"
                        class="settings-save-button"
                        data-toast-status="success"
                        data-toast-message="パスワードを変更しました"
                    >
                        保存
                    </button>
                </div>
                <div class="settings-card-body">
                    <div class="settings-grid">
                        <label class="settings-label" for="current-password">現在のパスワード</label>
                        <input
                            id="current-password"
                            type="password"
                            class="settings-input"
                            autocomplete="current-password"
                        />

                        <label class="settings-label" for="new-password">新しいパスワード</label>
                        <input
                            id="new-password"
                            type="password"
                            class="settings-input"
                            autocomplete="new-password"
                        />

                        <label class="settings-label" for="confirm-password">パスワードの確認</label>
                        <input
                            id="confirm-password"
                            type="password"
                            class="settings-input"
                            autocomplete="new-password"
                        />
                    </div>
                </div>
            </section>

            <section class="settings-card">
                <div class="settings-card-header">
                    <h3 class="settings-card-title">アカウント削除</h3>
                </div>
                <div class="settings-card-body">
                    <p class="settings-muted">
                        アカウント削除を行うと、登録したデータはすべて削除されます。
                    </p>
                    <button type="button" class="settings-danger-button">
                        DELETE ACCOUNT
                    </button>
                </div>
            </section>
        </div>
    </div>

    <div class="settings-toast-stack" aria-live="polite" aria-atomic="true">
        <div id="settings-toast" class="settings-toast" role="status">
            <span class="settings-toast-label"></span>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/settings.js')
    @endpush
</x-app-layout>
