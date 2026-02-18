<div class="flex items-center justify-between">
    <div>
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
    <div class="flex flex-col items-end gap-2">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.qualifications.template') }}"
                class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                テンプレートDL
            </a>
            <form method="POST" action="{{ route('admin.qualifications.import') }}"
                enctype="multipart/form-data" class="flex items-center gap-2" data-admin-csv-form>
                @csrf
                <input type="file" name="csv_file" accept=".csv,text/csv" class="hidden"
                    data-admin-csv-input>
                <button type="button"
                    class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                    data-admin-csv-trigger>
                    CSVアップロード
                </button>
            </form>
        </div>
        <button type="button"
            class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
            data-admin-create-open>
            @if ($mode === 'qualification')
                資格追加
            @elseif ($mode === 'domain')
                分野追加
            @else
                サブ分野追加
            @endif
        </button>
    </div>
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
