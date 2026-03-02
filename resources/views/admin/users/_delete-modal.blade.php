<div id="admin-users-delete-modal" class="modal-backdrop hidden" data-admin-users-delete-modal>
    <div class="modal-panel relative admin-users-delete-panel">
        <button type="button" class="modal-close" data-admin-users-delete-close aria-label="閉じる">×</button>
        <h2 class="modal-title">削除してもよろしいですか？</h2>
        <div class="modal-form">
            <p class="admin-users-delete-message">
                以下のユーザーを削除します。この操作は取り消せません。
            </p>
            <div class="modal-row">
                <label class="modal-label">名前</label>
                <span class="admin-users-delete-value" data-admin-users-delete-name></span>
            </div>
            <div class="modal-row">
                <label class="modal-label">メールアドレス</label>
                <span class="admin-users-delete-value" data-admin-users-delete-email></span>
            </div>
        </div>
        <div class="modal-actions admin-users-delete-actions">
            <button type="button" class="modal-secondary" data-admin-users-delete-close>戻る</button>
            <button type="button" class="modal-primary admin-danger-button" data-admin-users-delete-confirm>
                削除する
            </button>
        </div>
    </div>
</div>
