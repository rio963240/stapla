{{-- 確認画面 --}}
<div id="admin-users-confirm" class="admin-users-confirm is-hidden">
    <button type="button" class="admin-users-back-link" data-admin-users-back
        aria-label="編集画面に戻る">
        <span class="admin-users-back-link-icon" aria-hidden="true">←</span>
        編集に戻る
    </button>
    <h2 class="admin-users-panel-title">変更内容の確認</h2>

    <div class="admin-users-confirm-card admin-users-card">
        <p class="admin-users-card-label">変更内容</p>
        <p class="admin-users-card-desc">以下の内容で更新されます</p>
        <dl class="admin-users-confirm-list">
            <div class="admin-users-confirm-row">
                <dt>名前</dt>
                <dd data-admin-confirm-name></dd>
            </div>
            <div class="admin-users-confirm-row">
                <dt>メール</dt>
                <dd data-admin-confirm-email></dd>
            </div>
            <div class="admin-users-confirm-row">
                <dt>権限</dt>
                <dd data-admin-confirm-role></dd>
            </div>
            <div class="admin-users-confirm-row">
                <dt>状態</dt>
                <dd data-admin-confirm-status></dd>
            </div>
            <div class="admin-users-confirm-row">
                <dt>パスワード</dt>
                <dd data-admin-confirm-password></dd>
            </div>
        </dl>
        <div class="admin-users-form-actions admin-users-confirm-actions">
            <button type="button" class="admin-button-primary" data-admin-users-update>更新する</button>
            <button type="button" class="admin-button-secondary" data-admin-users-back>戻る</button>
        </div>
    </div>
</div>
