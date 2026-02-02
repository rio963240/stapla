<div id="plan-register-domain-modal" class="modal-backdrop hidden">
    <div class="modal-panel relative">
        <button type="button" class="modal-close" data-modal-close aria-label="閉じる">×</button>
        <h2 class="modal-title">計画登録（分野単位）</h2>
        <div class="modal-form">
            <div class="modal-row">
                <label class="modal-label">勉強開始日</label>
                <input type="date" class="modal-input" />
            </div>
            <div class="modal-row">
                <label class="modal-label">資格名</label>
                <input type="text" class="modal-input" placeholder="例：基本情報技術者" />
            </div>
            <div class="modal-row">
                <label class="modal-label">分野</label>
                <div class="modal-inline">
                    <select class="modal-input">
                        <option>選択してください</option>
                    </select>
                    <button type="button" class="modal-icon-button" aria-label="分野を追加">+</button>
                </div>
            </div>
            <div class="modal-row">
                <label class="modal-label">1日の学習時間</label>
                <div class="modal-inline">
                    <input type="number" class="modal-input" placeholder="60" />
                    <span class="modal-unit">分</span>
                </div>
            </div>
            <div class="modal-row">
                <label class="modal-label">
                    余裕確保
                    <span class="modal-help">？</span>
                </label>
                <div class="modal-inline">
                    <input type="number" class="modal-input" placeholder="10" />
                    <span class="modal-unit">%</span>
                </div>
            </div>
            <div class="modal-row">
                <label class="modal-label">勉強不可日</label>
                <div class="modal-stack">
                    <div class="modal-inline">
                        <input type="date" class="modal-input" data-no-study-input="domain" />
                        <button
                            type="button"
                            class="modal-icon-button"
                            data-no-study-add="domain"
                            aria-label="勉強不可日を追加"
                        >
                            +
                        </button>
                    </div>
                    <div class="modal-chip-list" data-no-study-list="domain"></div>
                </div>
            </div>
            <div class="modal-row">
                <label class="modal-label">受験日</label>
                <input type="date" class="modal-input" />
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="modal-secondary" data-modal-close>キャンセル</button>
            <button type="button" class="modal-primary">生成</button>
        </div>
    </div>
</div>
