{{-- 編集画面 --}}
<div id="admin-users-edit" class="admin-users-edit is-hidden">
    <button type="button" class="admin-users-back-link" data-admin-users-cancel
        aria-label="一覧に戻る">
        <span class="admin-users-back-link-icon" aria-hidden="true">←</span>
        一覧に戻る
    </button>
    <h2 class="admin-users-panel-title">ユーザー編集</h2>

    <div class="admin-users-edit-card admin-users-card">
        <p class="admin-users-card-label">ユーザー情報</p>
        <p class="admin-users-card-desc">変更不可の項目です</p>
        <div class="admin-users-info-grid">
            <div class="admin-users-info-item">
                <span class="admin-users-info-label">名前</span>
                <span class="admin-users-info-value" data-admin-edit-name></span>
            </div>
            <div class="admin-users-info-item">
                <span class="admin-users-info-label">メールアドレス</span>
                <span class="admin-users-info-value" data-admin-edit-email></span>
            </div>
        </div>
    </div>

    <div class="admin-users-edit-card admin-users-card">
        <p class="admin-users-card-label">編集項目</p>
        <p class="admin-users-card-desc">権限・状態・パスワードを変更できます</p>
        <form class="admin-users-edit-form" data-admin-users-edit-form>
            @csrf
            <input type="hidden" name="user_id" data-admin-edit-user-id>
            <div class="admin-users-form-group">
                <label for="admin-edit-role">権限</label>
                <select id="admin-edit-role" name="is_admin" class="admin-users-select"
                    data-admin-edit-role>
                    <option value="0">一般</option>
                    <option value="1">管理者</option>
                </select>
            </div>
            <div class="admin-users-form-group">
                <label for="admin-edit-status">状態</label>
                <select id="admin-edit-status" name="is_active" class="admin-users-select"
                    data-admin-edit-status>
                    <option value="0">停止</option>
                    <option value="1">有効</option>
                </select>
            </div>
            <div class="admin-users-form-group">
                <label for="admin-edit-password">パスワード</label>
                <input type="password" id="admin-edit-password" name="password"
                    class="admin-users-input" placeholder="変更する場合のみ入力"
                    data-admin-edit-password autocomplete="new-password">
                <p class="admin-users-form-hint">空欄のままの場合は変更されません</p>
            </div>
            <div class="admin-users-form-actions">
                <button type="submit" class="admin-button-primary" data-admin-users-confirm>確認</button>
                <button type="button" class="admin-button-secondary" data-admin-users-cancel>
                    キャンセル
                </button>
            </div>
        </form>
    </div>
</div>
