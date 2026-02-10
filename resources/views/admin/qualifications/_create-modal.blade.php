@php($createType = $mode === 'qualification' ? 'qualification' : ($mode === 'domain' ? 'domain' : 'subdomain'))
@php($createTitle = $mode === 'qualification' ? '資格追加' : ($mode === 'domain' ? '分野追加' : 'サブ分野追加'))
@php($createLabel = $mode === 'qualification' ? '資格名' : ($mode === 'domain' ? '分野名' : 'サブ分野名'))
@php($createAction = $mode === 'qualification'
    ? route('admin.qualifications.store')
    : ($mode === 'domain'
        ? route('admin.domains.store')
        : route('admin.subdomains.store')))

<div id="admin-qualification-create-modal" class="modal-backdrop hidden" data-admin-create-modal>
    <div class="modal-panel relative admin-qualification-panel">
        <button type="button" class="modal-close" data-admin-create-close aria-label="閉じる">×</button>

        <form method="POST" action="{{ $createAction }}" data-admin-create-form>
            @csrf

            <div class="admin-qualification-panel-body" data-admin-create-edit-panel>
                <h2 class="modal-title">{{ $createTitle }}</h2>
                <div class="modal-form">
                    @if ($mode !== 'qualification')
                        <div class="modal-row">
                            <label class="modal-label">資格</label>
                            <input type="text" class="modal-input" value="{{ $qualification?->name ?? '' }}"
                                data-admin-create-qualification readonly />
                            <input type="hidden" name="qualification_id"
                                value="{{ $qualification?->qualification_id ?? '' }}" />
                        </div>
                    @endif
                    @if ($mode === 'subdomain')
                        <div class="modal-row">
                            <label class="modal-label">分野</label>
                            <input type="text" class="modal-input" value="{{ $domain?->name ?? '' }}"
                                data-admin-create-domain readonly />
                            <input type="hidden" name="qualification_domains_id"
                                value="{{ $domain?->qualification_domains_id ?? '' }}" />
                        </div>
                    @endif
                    <div class="modal-row">
                        <label class="modal-label">{{ $createLabel }}</label>
                        <input type="text" name="name" class="modal-input" data-admin-create-name-input />
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="modal-secondary" data-admin-create-confirm-open>確認</button>
                    <button type="button" class="modal-secondary" data-admin-create-close>キャンセル</button>
                </div>
            </div>

            <div class="admin-qualification-panel-body hidden" data-admin-create-confirm-panel>
                <h2 class="modal-title">登録してよろしいですか？</h2>
                <div class="modal-form">
                    @if ($mode !== 'qualification')
                        <div class="modal-row">
                            <label class="modal-label">資格</label>
                            <input type="text" class="modal-input" data-admin-create-confirm-qualification
                                readonly />
                        </div>
                    @endif
                    @if ($mode === 'subdomain')
                        <div class="modal-row">
                            <label class="modal-label">分野</label>
                            <input type="text" class="modal-input" data-admin-create-confirm-domain readonly />
                        </div>
                    @endif
                    <div class="modal-row">
                        <label class="modal-label">{{ $createLabel }}</label>
                        <input type="text" class="modal-input" data-admin-create-confirm-name readonly />
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="modal-secondary" data-admin-create-confirm-back>戻る</button>
                    <button type="submit" class="modal-primary">登録</button>
                </div>
            </div>
        </form>
    </div>
</div>
