<div class="mt-6 overflow-hidden rounded-lg border border-gray-200">
    <table class="admin-qualification-table min-w-full divide-y divide-gray-200 text-sm">
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
                <th class="px-4 py-3 text-center admin-table-action">編集</th>
                <th class="px-4 py-3 text-center admin-table-action">
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
                        <td class="px-4 py-3 text-center admin-table-action">
                            <button type="button"
                                class="rounded-md border border-gray-300 px-3 py-1 text-gray-700 hover:bg-gray-50 js-admin-edit-trigger"
                                data-type="qualification"
                                data-name="{{ $item->name }}"
                                data-update-url="{{ route('admin.qualifications.update', $item) }}"
                                data-delete-url="{{ route('admin.qualifications.destroy', $item) }}">
                                編集
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center admin-table-action">
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
                        <td class="px-4 py-3 text-center admin-table-action">
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
                        <td class="px-4 py-3 text-center admin-table-action">
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
                        <td class="px-4 py-3 text-center admin-table-action">
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
                        <td class="px-4 py-3 text-center text-gray-400 admin-table-action">-</td>
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
