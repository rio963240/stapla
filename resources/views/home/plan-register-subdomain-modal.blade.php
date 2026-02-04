<div id="plan-register-subdomain-modal" class="modal-backdrop hidden">
    <div class="modal-panel relative">
        <button type="button" class="modal-close" data-modal-close aria-label="閉じる">×</button>
        <h2 class="modal-title">計画登録（サブ分野単位）</h2>
        <div class="modal-form">
            <div class="modal-row">
                <label class="modal-label">勉強開始日</label>
                <input type="date" class="modal-input" data-plan-start="subdomain" />
            </div>
            <div class="modal-row">
                <label class="modal-label">資格名</label>
                <select class="modal-input" data-qualification-select="subdomain">
                    <option value="">選択してください</option>
                    @foreach ($qualifications as $qualification)
                        <option value="{{ $qualification->qualification_id }}">{{ $qualification->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-row">
                <label class="modal-label">
                    サブ分野
                    <span class="modal-help-wrapper">
                        <button
                            type="button"
                            class="modal-help"
                            data-popover-target="weight-subdomain"
                            aria-expanded="false"
                            aria-controls="weight-subdomain-popover"
                            aria-label="重みの説明"
                        >
                            ？
                        </button>
                        <div id="weight-subdomain-popover" class="modal-popover hidden" role="tooltip">
                            重みとは、サブ分野ごとの学習量を<br />
                            相対的に調整するための係数です。<br />
                            <br />
                            全サブ分野の重みの合計を基準に、<br />
                            各サブ分野の学習時間が自動配分されます。<br />
                            <br />
                            例：<br />
                            重みの合計が「3」で、<br />
                            あるサブ分野の重みが「2」の場合、<br />
                            全体の約2/3がそのサブ分野に割り当てられます。
                        </div>
                    </span>
                </label>
                <div class="modal-stack" data-repeat-container="subdomain">
                    <div class="modal-inline" data-repeat-row>
                        <select class="modal-input" data-subdomain-select="subdomain">
                            <option>選択してください</option>
                        </select>
                        <input
                            type="number"
                            class="modal-input modal-input-short"
                            placeholder="重み"
                            data-subdomain-weight="subdomain"
                        />
                        <button
                            type="button"
                            class="modal-icon-button modal-icon-button-round"
                            data-repeat-add="subdomain"
                            aria-label="サブ分野を追加"
                        >
                            +
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-row">
                <label class="modal-label">1日の学習時間</label>
                <div class="modal-inline">
                    <input type="number" class="modal-input" placeholder="60" data-daily-study="subdomain" />
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
                            data-popover-target="buffer-subdomain"
                            aria-expanded="false"
                            aria-controls="buffer-subdomain-popover"
                            aria-label="余裕確保の説明"
                        >
                            ？
                        </button>
                        <div id="buffer-subdomain-popover" class="modal-popover hidden" role="tooltip">
                            余裕確保とは、計画にあらかじめ「予備時間」を組み込むための設定です。<br />
                            <br />
                            例：10％に設定すると、<br />
                            全体の学習量に対して約10％分の<br />
                            余裕を持った計画になります。
                        </div>
                    </span>
                </label>
                <div class="modal-inline">
                    <input type="number" class="modal-input" placeholder="10" data-buffer-rate="subdomain" />
                    <span class="modal-unit">%</span>
                </div>
            </div>
            <div class="modal-row">
                <label class="modal-label">勉強不可日</label>
                <div class="modal-stack">
                    <div class="modal-inline">
                        <input type="date" class="modal-input" data-no-study-input="subdomain" />
                        <button
                            type="button"
                            class="modal-icon-button"
                            data-no-study-add="subdomain"
                            aria-label="勉強不可日を追加"
                        >
                            +
                        </button>
                    </div>
                    <div class="modal-chip-list" data-no-study-list="subdomain"></div>
                </div>
            </div>
            <div class="modal-row">
                <label class="modal-label">受験日</label>
                <input type="date" class="modal-input" data-plan-exam="subdomain" />
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="modal-secondary" data-modal-close>キャンセル</button>
            <button type="button" class="modal-primary" data-plan-submit="subdomain">生成</button>
        </div>
    </div>
</div>
