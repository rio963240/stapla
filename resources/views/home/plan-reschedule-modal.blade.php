<div id="plan-reschedule-modal" class="modal-backdrop hidden">
    <div class="modal-panel relative">
        <button type="button" class="modal-close" data-reschedule-close aria-label="閉じる">×</button>
        <h2 class="modal-title">リスケジュール</h2>
        <div class="modal-form">
            {{-- 対象資格の選択 --}}
            <div class="modal-row">
                <label class="modal-label">対象資格</label>
                <div class="modal-stack">
                    <select class="modal-input" data-reschedule-target>
                        <option value="">選択してください</option>
                        @foreach ($targets as $target)
                            @if ($target->active_plan_id)
                                <option value="{{ $target->user_qualification_targets_id }}">
                                    {{ $target->qualification_name }}（試験日: {{ $target->exam_date }}）
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @if ($targets->whereNotNull('active_plan_id')->isEmpty())
                        <p class="modal-note-text">リスケジュール可能な計画がありません。</p>
                    @endif
                </div>
            </div>
            {{-- 表示のみ（固定情報） --}}
            <div class="modal-row">
                <label class="modal-label">資格名</label>
                <input type="text" class="modal-input" data-reschedule-qualification readonly />
            </div>
            <div class="modal-row">
                <label class="modal-label">リスケ開始日</label>
                <input type="date" class="modal-input" data-reschedule-start-date />
            </div>
            <div class="modal-row">
                <label class="modal-label">受験日</label>
                <input type="text" class="modal-input" data-reschedule-exam readonly />
            </div>
            {{-- 入力可（リスケ条件） --}}
            <div class="modal-row">
                <label class="modal-label">1日の学習時間</label>
                <div class="modal-inline">
                    <input type="number" class="modal-input" placeholder="60" data-reschedule-daily />
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
                            data-popover-target="buffer-reschedule"
                            aria-expanded="false"
                            aria-controls="buffer-reschedule-popover"
                            aria-label="余裕確保の説明"
                        >
                            ？
                        </button>
                        <div id="buffer-reschedule-popover" class="modal-popover hidden" role="tooltip">
                            余裕確保とは、計画にあらかじめ「予備時間」を組み込むための設定です。<br />
                            <br />
                            例：10％に設定すると、<br />
                            全体の学習量に対して約10％分の<br />
                            余裕を持った計画になります。
                        </div>
                    </span>
                </label>
                <div class="modal-inline">
                    <input type="number" class="modal-input" placeholder="10" data-reschedule-buffer />
                    <span class="modal-unit">%</span>
                </div>
            </div>
            <div class="modal-row">
                <label class="modal-label">勉強不可日</label>
                <div class="modal-stack">
                    <div class="modal-inline">
                        <input type="date" class="modal-input" data-reschedule-no-study-input />
                        <button
                            type="button"
                            class="modal-icon-button"
                            data-reschedule-no-study-add
                            aria-label="勉強不可日を追加"
                        >
                            +
                        </button>
                    </div>
                    <div class="modal-chip-list" data-reschedule-no-study-list></div>
                </div>
            </div>
            <div class="modal-row">
                <label class="modal-label">
                    <span data-reschedule-weight-type-label>分野</span>
                </label>
                <div class="modal-stack" data-reschedule-weights></div>
            </div>
            {{-- 注意事項 --}}
            <div class="modal-row">
                <label class="modal-label">案内</label>
                <div class="modal-note">
                    <ul class="modal-note-list">
                        <li>明日以降の計画を削除して、条件に合わせて作り直します。</li>
                        <li>今日以前の計画と学習実績は変更されません。</li>
                        <li>受験日は計画に含まれません（試験前日まで作成）。</li>
                        <li>今日の未達分は残量として明日以降に再配分されます。</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="modal-secondary" data-reschedule-close>キャンセル</button>
            <button type="button" class="modal-primary" data-reschedule-submit>リスケジュール</button>
        </div>
    </div>
</div>
