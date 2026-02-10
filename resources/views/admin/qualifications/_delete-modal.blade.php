<div id="admin-qualification-delete-modal" class="modal-backdrop hidden" data-admin-delete-modal>
    <div class="modal-panel relative admin-qualification-panel">
        <button type="button" class="modal-close" data-admin-delete-close aria-label="閉じる">×</button>
        <h2 class="modal-title">削除してもよろしいですか？</h2>
        <form method="POST" data-admin-delete-form>
            @csrf
            @method('DELETE')

            <div class="modal-form">
                <div class="modal-row" data-admin-delete-qualification-row>
                    <label class="modal-label">資格</label>
                    <input type="text" class="modal-input" data-admin-delete-qualification readonly />
                </div>
                <div class="modal-row hidden" data-admin-delete-domain-row>
                    <label class="modal-label">分野</label>
                    <input type="text" class="modal-input" data-admin-delete-domain readonly />
                </div>
                <div class="modal-row">
                    <label class="modal-label" data-admin-delete-name-label>名称</label>
                    <input type="text" class="modal-input" data-admin-delete-name readonly />
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="modal-secondary" data-admin-delete-close>戻る</button>
                <button type="submit" class="modal-primary admin-danger-button">削除</button>
            </div>
        </form>
    </div>
</div>
