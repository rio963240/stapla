<div id="study-record-modal" class="study-record-popover is-hidden" data-study-record-modal>
    <div class="study-record-popover-panel study-record-panel" role="dialog" aria-modal="true">
        <button type="button" class="study-record-popover-close" data-study-record-close aria-label="閉じる">
            ×
        </button>

        <div class="study-record-step" data-study-record-step="summary">
            <div class="study-record-meta">
                <div class="study-record-title" data-study-record-title></div>
                <div class="study-record-date" data-study-record-date></div>
            </div>

            <div class="modal-form study-record-form">
                <div class="study-record-list" data-study-record-summary-list></div>
            </div>

            <div class="modal-actions">
                <button type="button" class="modal-secondary" data-study-record-close>閉じる</button>
                <button type="button" class="modal-primary" data-study-record-next>実績入力</button>
            </div>
        </div>

        <div class="study-record-step hidden" data-study-record-step="form">
            <div class="study-record-meta">
                <div class="study-record-title" data-study-record-title></div>
                <div class="study-record-date" data-study-record-date></div>
            </div>

            <div class="modal-form study-record-form">
                <div class="study-record-list" data-study-record-form-list></div>
                <div class="study-record-memo">
                    <label class="modal-label" for="study-record-memo">備考</label>
                    <textarea
                        id="study-record-memo"
                        class="modal-input study-record-textarea"
                        rows="3"
                        data-study-record-memo
                    ></textarea>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="modal-secondary" data-study-record-back>戻る</button>
                <button type="button" class="modal-primary" data-study-record-save>保存</button>
            </div>
        </div>
    </div>
</div>
