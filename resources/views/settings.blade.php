<x-app-layout>
    {{-- 設定画面専用のCSS --}}
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
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            {{-- 基本情報（名前・アイコン）更新フォーム --}}
            <form method="POST" action="{{ route('settings.basic.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <section class="settings-card">
                    <div class="settings-card-header">
                        <h3 class="settings-card-title">基本情報</h3>
                        <button type="submit" class="settings-save-button">
                            保存
                        </button>
                    </div>
                    <div class="settings-card-body">
                        <div class="settings-avatar">
                            <img src="{{ auth()->user()?->profile_photo_url ?? asset('images/no-image.jpeg') }}"
                                alt="プロフィール" class="settings-avatar-image" data-settings-avatar />
                            <div class="settings-avatar-actions">
                                <button type="button" class="settings-secondary-button" data-settings-photo-trigger>
                                    アイコン変更
                                </button>
                                <p class="settings-muted">JPEG/PNG 5MB まで</p>
                            </div>
                        </div>
                        <input id="settings-photo" name="photo" type="file" class="sr-only"
                            accept="image/jpeg,image/png" />

                        <div class="settings-grid">
                            <label class="settings-label" for="settings-name">名前</label>
                            <input id="settings-name" name="name" type="text" class="settings-input"
                                value="{{ old('name', auth()->user()?->name ?? '') }}" required data-settings-name />

                            <label class="settings-label" for="settings-email">E-mail</label>
                            <input id="settings-email" type="email" class="settings-input settings-input-disabled"
                                value="{{ auth()->user()?->email ?? '' }}" disabled />
                        </div>
                    </div>
                </section>
            </form>

            {{-- 通知設定（LINE連携・通知時間保存） --}}
            @php
                $user = auth()->user();
                $lineMorning = $user?->line_morning_time ? \Carbon\Carbon::parse($user->line_morning_time)->format('H:i') : '08:00';
                $lineEvening = $user?->line_evening_time ? \Carbon\Carbon::parse($user->line_evening_time)->format('H:i') : '20:00';
                $lineSection = ($lineAccount?->line_user_id ?? false) ? 'linked' : (($lineLinkToken ?? null) ? 'token' : 'start');
                $lineAddFriendUrl = config('services.line.add_friend_url', '');
            @endphp
            <section class="settings-card">
                <div class="settings-card-header">
                    <h3 class="settings-card-title">通知設定</h3>
                    <button type="submit" form="settings-notifications-form" class="settings-save-button">
                        保存
                    </button>
                </div>
                <div class="settings-card-body settings-notify">
                    {{-- LINE連携ブロック（フォームの外に出すことで「LINE連携を開始」が正しく line-link に送信される） --}}
                    <div class="settings-qr-block">
                        <p class="settings-label">LINE連携</p>
                        @if ($lineSection === 'linked')
                            <p class="settings-muted">LINEと連携済みです。朝・夜の通知をお届けします。</p>
                        @elseif ($lineSection === 'token')
                            <p class="settings-label mt-2">QRコードを読み取るか、下のボタンから友だち追加してください</p>
                            <img src="{{ asset(config('services.line.qr_image', 'images/line-qr.png')) }}" alt="LINE友だち追加QRコード"
                                class="mt-2 w-40 h-40 object-contain border border-gray-200 rounded-lg" />
                            <p class="settings-label mt-3">友だち追加後、以下のコードを送信してください</p>
                            <p class="text-lg font-mono font-bold my-2">{{ $lineLinkToken }}</p>
                            @if ($lineAddFriendUrl !== '')
                            <a href="{{ $lineAddFriendUrl }}" target="_blank" rel="noopener"
                                class="inline-block mt-2 px-4 py-2 bg-[#06C755] text-white rounded-lg text-sm font-medium">
                                友だち追加はこちら
                            </a>
                            @endif
                            <p class="settings-muted mt-2">1. QRコードまたはボタンで友だち追加 2. コードをそのまま送信</p>
                        @else
                            <form method="POST" action="{{ route('settings.line-link') }}" class="inline">
                                @csrf
                                <button type="submit" class="settings-secondary-button">
                                    LINE連携を開始
                                </button>
                            </form>
                            <p class="settings-muted mt-2">LINEで友だち追加して朝・夜の通知を受け取ります</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('settings.notifications.update') }}" id="settings-notifications-form">
                        @csrf
                        @method('PUT')
                    <div class="settings-grid">
                        <label class="settings-label" for="line-notify">LINE通知</label>
                        <label class="settings-switch">
                            <input id="line-notify" name="line_notify_enabled" type="checkbox" value="1" data-time-toggle="line" @checked($user->line_notify_enabled ?? false) />
                            <span class="settings-slider"></span>
                        </label>

                        <div class="settings-time-block" data-time-block="line">
                            <p class="settings-label">LINE通知時間</p>
                            <div class="settings-time-selects">
                                <div class="settings-time-select">
                                    <span class="settings-time-tag">朝</span>
                                    <select class="settings-select" name="line_morning_at" aria-label="LINE通知 朝">
                                        @foreach (range(0, 23) as $hour)
                                            <option value="{{ sprintf('%02d:00', $hour) }}" @selected(sprintf('%02d:00', $hour) === $lineMorning)>
                                                {{ sprintf('%02d:00', $hour) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="settings-time-select">
                                    <span class="settings-time-tag">夜</span>
                                    <select class="settings-select" name="line_evening_at" aria-label="LINE通知 夜">
                                        @foreach (range(0, 23) as $hour)
                                            <option value="{{ sprintf('%02d:00', $hour) }}" @selected(sprintf('%02d:00', $hour) === $lineEvening)>
                                                {{ sprintf('%02d:00', $hour) }}</option>
                                        @endforeach
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
                                        @foreach (range(0, 23) as $hour)
                                            <option value="{{ sprintf('%02d:00', $hour) }}" @selected(sprintf('%02d:00', $hour) === '08:00')>
                                                {{ sprintf('%02d:00', $hour) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="settings-time-select">
                                    <span class="settings-time-tag">夜</span>
                                    <select class="settings-select" aria-label="メール通知 夜">
                                        @foreach (range(0, 23) as $hour)
                                            <option value="{{ sprintf('%02d:00', $hour) }}" @selected(sprintf('%02d:00', $hour) === '20:00')>
                                                {{ sprintf('%02d:00', $hour) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </section>

            {{-- パスワード変更フォーム --}}
            <form method="POST" action="{{ route('settings.password.update') }}">
                @csrf
                @method('PUT')
                <section class="settings-card">
                    <div class="settings-card-header">
                        <h3 class="settings-card-title">パスワード変更</h3>
                        <button type="submit" class="settings-save-button">
                            保存
                        </button>
                    </div>
                    <div class="settings-card-body">
                        <div class="settings-grid">
                            <label class="settings-label" for="current-password">現在のパスワード</label>
                            <input id="current-password" name="current_password" type="password"
                                class="settings-input" autocomplete="current-password" required />

                            <label class="settings-label" for="new-password">新しいパスワード</label>
                            <input id="new-password" name="password" type="password" class="settings-input"
                                autocomplete="new-password" required />

                            <label class="settings-label" for="confirm-password">パスワードの確認</label>
                            <input id="confirm-password" name="password_confirmation" type="password"
                                class="settings-input" autocomplete="new-password" required />
                        </div>
                    </div>
                </section>
            </form>

            {{-- アカウント削除への導線 --}}
            <section class="settings-card">
                <div class="settings-card-header">
                    <h3 class="settings-card-title">アカウント削除</h3>
                </div>
                <div class="settings-card-body">
                    <p class="settings-muted">
                        アカウント削除を行うと、登録したデータはすべて削除されます。
                    </p>
                    <button type="button" class="settings-danger-button" data-delete-modal-open>
                        アカウントを削除
                    </button>
                </div>
            </section>
        </div>
    </div>

    {{-- アカウント削除モーダル --}}
    <div class="settings-modal is-hidden" data-delete-modal>
        <div class="settings-modal-overlay" data-delete-modal-close></div>
        <div class="settings-modal-panel" role="dialog" aria-modal="true" aria-labelledby="delete-account-title">
            <div class="settings-modal-header">
                <h3 id="delete-account-title" class="settings-modal-title">アカウント削除</h3>
            </div>
            <p class="settings-modal-text">
                アカウントを削除してもよろしいですか？削除すると、アカウントに紐づくデータはすべて
                完全に削除されます。削除を確定するため、パスワードを入力してください。
            </p>
            <form method="POST" action="{{ route('settings.account.destroy') }}" data-delete-form>
                @csrf
                @method('DELETE')
                <input type="password" name="password" class="settings-input" placeholder="パスワード"
                    autocomplete="current-password" required data-delete-password />
                <p class="settings-modal-error" data-delete-error></p>
                <div class="settings-modal-actions">
                    <button type="button" class="settings-secondary-button" data-delete-modal-close>
                        キャンセル
                    </button>
                    <button type="submit" class="settings-modal-danger-button">
                        削除する
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 保存結果を通知するトースト --}}
    <div class="settings-toast-stack" aria-live="polite" aria-atomic="true">
        @php
            $toastShow = in_array(session('status'), ['basic-info-updated', 'notifications-updated'], true);
            $toastMessage = session('status') === 'notifications-updated' ? '通知設定を保存しました' : '基本情報を保存しました';
        @endphp
        <div id="settings-toast"
            class="settings-toast {{ $toastShow ? 'is-visible' : '' }}"
            role="status" data-toast-status="{{ $toastShow ? 'success' : '' }}"
            data-toast-message="{{ $toastMessage }}"
            data-toast-autoshow="{{ $toastShow ? 'true' : 'false' }}">
            <span class="settings-toast-label"></span>
        </div>
    </div>

    {{-- 設定画面専用のJS --}}
    @push('scripts')
        @vite('resources/js/settings.js')
    @endpush
</x-app-layout>
