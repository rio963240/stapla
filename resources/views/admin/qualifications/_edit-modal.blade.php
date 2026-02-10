<div id="admin-qualification-modal" class="modal-backdrop hidden" data-admin-modal>
    <div class="modal-panel relative admin-qualification-panel">
        <button type="button" class="modal-close" data-admin-modal-close aria-label="閉じる">×</button>

        <div class="admin-qualification-panel-body" data-admin-edit-panel>
            <h2 class="modal-title" data-admin-modal-title>編集</h2>
            <form method="POST" data-admin-edit-form>
                @csrf
                @method('PATCH')

                <div class="modal-form">
                    <div class="modal-row" data-admin-qualification-row>
                        <label class="modal-label">資格</label>
                        <input type="text" class="modal-input" data-admin-qualification-input readonly />
                    </div>
                    <div class="modal-row hidden" data-admin-domain-row>
                        <label class="modal-label">分野</label>
                        <input type="text" class="modal-input" data-admin-domain-input readonly />
                    </div>
                    <div class="modal-row" data-admin-name-row>
                        <label class="modal-label" data-admin-name-label>名称</label>
                        <input type="text" name="name" class="modal-input" data-admin-name-input />
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="modal-secondary" data-admin-delete-open>削除</button>
                    <button type="button" class="modal-secondary" data-admin-confirm-open>確認</button>
                    <button type="button" class="modal-secondary" data-admin-modal-close>キャンセル</button>
                </div>
            </form>
        </div>

        <div class="admin-qualification-panel-body hidden" data-admin-confirm-panel>
            <h2 class="modal-title">変更してもよろしいですか？</h2>
            <div class="modal-form">
                <div class="modal-row" data-admin-confirm-qualification-row>
                    <label class="modal-label">資格</label>
                    <input type="text" class="modal-input" data-admin-confirm-qualification readonly />
                </div>
                <div class="modal-row hidden" data-admin-confirm-domain-row>
                    <label class="modal-label">分野</label>
                    <input type="text" class="modal-input" data-admin-confirm-domain readonly />
                </div>
                <div class="modal-row">
                    <label class="modal-label" data-admin-confirm-name-label>名称</label>
                    <input type="text" class="modal-input" data-admin-confirm-name readonly />
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="modal-secondary" data-admin-confirm-back>戻る</button>
                <button type="button" class="modal-primary" data-admin-submit>変更</button>
            </div>
        </div>
    </div>
</div>
