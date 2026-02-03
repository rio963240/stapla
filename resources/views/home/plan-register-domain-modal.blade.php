<div id="plan-register-domain-modal" class="modal-backdrop hidden">
    <div class="modal-panel relative">
        <button type="button" class="modal-close" data-modal-close aria-label="閉じる">×</button>
        <h2 class="modal-title">計画登録（分野単位）</h2>
        <div class="modal-form">
            <div class="modal-row">
                <label class="modal-label">勉強開始日</label>
                <input type="date" class="modal-input" data-plan-start="domain" />
            </div>
            <div class="modal-row">
                <label class="modal-label">資格名</label>
                <select class="modal-input" data-qualification-select="domain">
                    <option value="">選択してください</option>
                    @foreach ($qualifications as $qualification)
                        <option value="{{ $qualification->qualification_id }}">{{ $qualification->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-row">
                <label class="modal-label">
                    分野
                    <span class="modal-help-wrapper">
                        <button
                            type="button"
                            class="modal-help"
                            data-popover-target="weight"
                            aria-expanded="false"
                            aria-controls="weight-popover"
                            aria-label="重みの説明"
                        >
                            ？
                        </button>
                        <div id="weight-popover" class="modal-popover hidden" role="tooltip">
                            重みとは、分野ごとの学習量を<br />
                            相対的に調整するための係数です。<br />
                            <br />
                            全分野の重みの合計を基準に、<br />
                            各分野の学習時間が自動配分されます。<br />
                            <br />
                            例：<br />
                            重みの合計が「3」で、<br />
                            ある分野の重みが「2」の場合、<br />
                            全体の約2/3がその分野に割り当てられます。
                        </div>
                    </span>
                </label>
                <div class="modal-stack" data-repeat-container="domain">
                    <div class="modal-inline" data-repeat-row>
                        <select class="modal-input" data-domain-select="domain">
                            <option>選択してください</option>
                        </select>
                        <input
                            type="number"
                            class="modal-input modal-input-short"
                            placeholder="重み"
                            data-domain-weight="domain"
                        />
                        <button
                            type="button"
                            class="modal-icon-button modal-icon-button-round"
                            data-repeat-add="domain"
                            aria-label="分野を追加"
                        >
                            +
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-row">
                <label class="modal-label">1日の学習時間</label>
                <div class="modal-inline">
                    <input type="number" class="modal-input" placeholder="60" data-daily-study="domain" />
                    <span class="modal-unit">分</span>
                </div>
            </div>
            <div class="modal-row">
                <label class="modal-label">
                    余裕確保
                    <span class="modal-help-wrapper">
                        <button
                            type="button"
                            class="modal-help"
                            data-popover-target="buffer"
                            aria-expanded="false"
                            aria-controls="buffer-popover"
                            aria-label="余裕確保の説明"
                        >
                            ？
                        </button>
                        <div id="buffer-popover" class="modal-popover hidden" role="tooltip">
                            余裕確保とは、計画にあらかじめ「予備時間」を組み込むための設定です。<br />
                            <br />
                            例：10％に設定すると、<br />
                            全体の学習量に対して約10％分の<br />
                            余裕を持った計画になります。
                        </div>
                    </span>
                </label>
                <div class="modal-inline">
                    <input type="number" class="modal-input" placeholder="10" data-buffer-rate="domain" />
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
                <input type="date" class="modal-input" data-plan-exam="domain" />
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="modal-secondary" data-modal-close>キャンセル</button>
            <button type="button" class="modal-primary" data-plan-submit="domain">生成</button>
        </div>
    </div>
</div>
