<x-app-layout :show-navigation="false">
    @php($toastStatus = session('toast_status') ?? ($errors->any() ? 'error' : null))
    @php($toastMessage = session('toast_message') ?? ($errors->any() ? $errors->first() : null))
    @push('styles')
        @vite('resources/css/admin/admin-qualifications.css')
    @endpush
    @php($isAdmin = true)
    <div class="h-screen bg-gray-100 overflow-hidden">
        <div class="mx-auto flex h-[calc(100vh-3rem)] max-w-7xl gap-8 px-6 py-6 sm:px-6 lg:px-8">
            @include('home.sidebar', ['context' => 'admin', 'isAdmin' => $isAdmin])

            <section class="flex-1 rounded-lg bg-white p-6 shadow-sm overflow-auto">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">資格情報管理</p>
                        <h1 class="text-xl font-semibold text-gray-800">
                            @if ($mode === 'qualification')
                                資格情報一覧
                            @elseif ($mode === 'domain')
                                {{ $qualification?->name ?? '' }} / 分野一覧
                            @else
                                {{ $qualification?->name ?? '' }} / {{ $domain?->name ?? '' }} / サブ分野一覧
                            @endif
                        </h1>
                    </div>
                    <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-400" disabled>
                        追加
                    </button>
                </div>

                <div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                    <a class="hover:text-gray-700" href="{{ route('admin.qualifications') }}">資格</a>
                    @if ($mode !== 'qualification')
                        <span>/</span>
                        <a class="hover:text-gray-700"
                            href="{{ route('admin.qualifications', ['qualification_id' => $qualification?->qualification_id]) }}">
                            分野
                        </a>
                    @endif
                    @if ($mode === 'subdomain')
                        <span>/</span>
                        <span class="text-gray-700">サブ分野</span>
                    @endif
                </div>

                <div class="mt-6 overflow-hidden rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">
                                    @if ($mode === 'qualification')
                                        資格
                                    @elseif ($mode === 'domain')
                                        分野
                                    @else
                                        サブ分野
                                    @endif
                                </th>
                                <th class="px-4 py-3 text-center">編集</th>
                                <th class="px-4 py-3 text-center">
                                    @if ($mode === 'qualification')
                                        分野へ
                                    @elseif ($mode === 'domain')
                                        サブ分野へ
                                    @else
                                        -
                                    @endif
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @if ($mode === 'qualification')
                                @forelse ($qualifications as $item)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item->name }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button"
                                                class="rounded-md border border-gray-300 px-3 py-1 text-gray-700 hover:bg-gray-50 js-admin-edit-trigger"
                                                data-type="qualification"
                                                data-name="{{ $item->name }}"
                                                data-update-url="{{ route('admin.qualifications.update', $item) }}"
                                                data-delete-url="{{ route('admin.qualifications.destroy', $item) }}">
                                                編集
                                            </button>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a class="rounded-md border border-gray-300 px-3 py-1 text-gray-700 hover:bg-gray-50"
                                                href="{{ route('admin.qualifications', ['qualification_id' => $item->qualification_id]) }}">
                                                分野へ
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-4 py-6 text-center text-gray-500" colspan="3">資格がありません。</td>
                                    </tr>
                                @endforelse
                            @elseif ($mode === 'domain')
                                @forelse ($domains as $item)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item->name }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button"
                                                class="rounded-md border border-gray-300 px-3 py-1 text-gray-700 hover:bg-gray-50 js-admin-edit-trigger"
                                                data-type="domain"
                                                data-name="{{ $item->name }}"
                                                data-qualification-name="{{ $qualification?->name ?? '' }}"
                                                data-update-url="{{ route('admin.domains.update', $item) }}"
                                                data-delete-url="{{ route('admin.domains.destroy', $item) }}">
                                                編集
                                            </button>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a class="rounded-md border border-gray-300 px-3 py-1 text-gray-700 hover:bg-gray-50"
                                                href="{{ route('admin.qualifications', ['qualification_id' => $qualification?->qualification_id, 'domain_id' => $item->qualification_domains_id]) }}">
                                                サブ分野へ
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-4 py-6 text-center text-gray-500" colspan="3">分野がありません。</td>
                                    </tr>
                                @endforelse
                            @else
                                @forelse ($subdomains as $item)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item->name }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button"
                                                class="rounded-md border border-gray-300 px-3 py-1 text-gray-700 hover:bg-gray-50 js-admin-edit-trigger"
                                                data-type="subdomain"
                                                data-name="{{ $item->name }}"
                                                data-qualification-name="{{ $qualification?->name ?? '' }}"
                                                data-domain-name="{{ $domain?->name ?? '' }}"
                                                data-update-url="{{ route('admin.subdomains.update', $item) }}"
                                                data-delete-url="{{ route('admin.subdomains.destroy', $item) }}">
                                                編集
                                            </button>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-400">-</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-4 py-6 text-center text-gray-500" colspan="3">サブ分野がありません。</td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    @push('modals')
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
    @endpush

    <div class="admin-toast-stack" aria-live="polite" aria-atomic="true">
        <div id="admin-toast"
            class="admin-toast {{ $toastStatus ? 'is-visible' : '' }} {{ $toastStatus === 'error' ? 'is-error' : '' }}"
            role="status"
            data-toast-status="{{ $toastStatus ?? '' }}"
            data-toast-message="{{ $toastMessage ?? '' }}"
            data-toast-autoshow="{{ $toastStatus ? 'true' : 'false' }}">
            <span class="admin-toast-label"></span>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/admin/admin-qualifications.js')
    @endpush
</x-app-layout>
