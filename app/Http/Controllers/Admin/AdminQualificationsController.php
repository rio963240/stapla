<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Qualification;
use App\Models\QualificationDomain;
use App\Models\QualificationSubdomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;
use SplFileObject;
use Throwable;

class AdminQualificationsController extends Controller
{
    public function index(Request $request)
    {
        // クエリに応じて表示対象（資格/分野/サブ分野）を切り替える
        $qualificationId = $request->query('qualification_id');
        $domainId = $request->query('domain_id');

        $mode = 'qualification';
        $qualification = null;
        $domain = null;
        $qualifications = collect();
        $domains = collect();
        $subdomains = collect();

        if ($qualificationId) {
            // 資格が指定された場合は分野一覧へ
            $qualification = Qualification::query()
                ->where('qualification_id', $qualificationId)
                ->firstOrFail();

            $mode = 'domain';
            $domains = QualificationDomain::query()
                ->where('qualification_id', $qualificationId)
                ->orderBy('name')
                ->get();

            if ($domainId) {
                // 分野が指定された場合はサブ分野一覧へ
                $domain = QualificationDomain::query()
                    ->where('qualification_domains_id', $domainId)
                    ->where('qualification_id', $qualificationId)
                    ->firstOrFail();

                $mode = 'subdomain';
                $subdomains = QualificationSubdomain::query()
                    ->where('qualification_domains_id', $domainId)
                    ->orderBy('name')
                    ->get();
            }
        } else {
            // 資格一覧表示
            $qualifications = Qualification::query()
                ->orderBy('name')
                ->get();
        }

        return view('admin.qualifications', compact(
            'mode',
            'qualification',
            'domain',
            'qualifications',
            'domains',
            'subdomains',
        ));
    }

    public function updateQualification(Request $request, Qualification $qualification): RedirectResponse
    {
        // 資格名の更新
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $qualification->update([
                'name' => $data['name'],
            ]);
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '変更に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '変更しました',
        ]);
    }

    public function updateDomain(Request $request, QualificationDomain $domain): RedirectResponse
    {
        // 分野名の更新
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $domain->update([
                'name' => $data['name'],
            ]);
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '変更に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '変更しました',
        ]);
    }

    public function updateSubdomain(Request $request, QualificationSubdomain $subdomain): RedirectResponse
    {
        // サブ分野名の更新
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $subdomain->update([
                'name' => $data['name'],
            ]);
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '変更に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '変更しました',
        ]);
    }

    public function destroyQualification(Qualification $qualification): RedirectResponse
    {
        // 資格の削除
        try {
            $qualification->delete();
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '削除に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '削除しました',
        ]);
    }

    public function destroyDomain(QualificationDomain $domain): RedirectResponse
    {
        // 分野の削除
        try {
            $domain->delete();
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '削除に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '削除しました',
        ]);
    }

    public function destroySubdomain(QualificationSubdomain $subdomain): RedirectResponse
    {
        // サブ分野の削除
        try {
            $subdomain->delete();
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '削除に失敗しました',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '削除しました',
        ]);
    }

    public function storeQualification(Request $request): RedirectResponse
    {
        // 資格の新規登録
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $qualification = Qualification::firstOrCreate(
                ['name' => $data['name']],
                ['is_active' => true],
            );
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '登録に失敗しました',
            ]);
        }

        if (!$qualification->wasRecentlyCreated) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => 'すでに登録されています',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '登録しました',
        ]);
    }

    public function storeDomain(Request $request): RedirectResponse
    {
        // 分野の新規登録
        $data = $request->validate([
            'qualification_id' => ['required', 'integer', 'exists:qualification,qualification_id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $domain = QualificationDomain::firstOrCreate(
                [
                    'qualification_id' => $data['qualification_id'],
                    'name' => $data['name'],
                ],
                ['is_active' => true],
            );
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '登録に失敗しました',
            ]);
        }

        if (!$domain->wasRecentlyCreated) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => 'すでに登録されています',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '登録しました',
        ]);
    }

    public function storeSubdomain(Request $request): RedirectResponse
    {
        // サブ分野の新規登録
        $data = $request->validate([
            'qualification_id' => ['required', 'integer', 'exists:qualification,qualification_id'],
            'qualification_domains_id' => ['required', 'integer', 'exists:qualification_domains,qualification_domains_id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        // 資格と分野の整合性チェック
        $domain = QualificationDomain::query()
            ->where('qualification_domains_id', $data['qualification_domains_id'])
            ->where('qualification_id', $data['qualification_id'])
            ->first();

        if (!$domain) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '登録先の分野が見つかりません',
            ]);
        }

        try {
            $subdomain = QualificationSubdomain::firstOrCreate(
                [
                    'qualification_domains_id' => $domain->qualification_domains_id,
                    'name' => $data['name'],
                ],
                ['is_active' => true],
            );
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => '登録に失敗しました',
            ]);
        }

        if (!$subdomain->wasRecentlyCreated) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => 'すでに登録されています',
            ]);
        }

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => '登録しました',
        ]);
    }

    public function importCsv(Request $request): RedirectResponse
    {
        // CSVアップロードのバリデーション
        $data = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $data['csv_file'];
        $added = 0;
        $duplicates = 0;
        $skipped = 0;

        try {
            // CSV読み込みとヘッダチェック
            $csv = new SplFileObject($file->getRealPath());
            $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

            $header = null;
            $indexes = [];
            $requiredHeaders = ['qualification_name', 'domain_name', 'subdomain_name'];

            foreach ($csv as $row) {
                if (!is_array($row)) {
                    continue;
                }

                if ($header === null) {
                    // ヘッダ行の検証とカラム位置の確定
                    $header = array_map('trim', $row);
                    if (isset($header[0])) {
                        $header[0] = ltrim($header[0], "\xEF\xBB\xBF");
                    }

                    $missing = array_diff($requiredHeaders, $header);
                    if (!empty($missing)) {
                        throw new RuntimeException('CSVヘッダーが不正です。');
                    }

                    $indexes = array_flip($header);
                    continue;
                }

                if (count($row) === 1 && ($row[0] === null || trim((string) $row[0]) === '')) {
                    continue;
                }

                // 必須列が空ならスキップ
                $qualificationName = trim((string) ($row[$indexes['qualification_name']] ?? ''));
                $domainName = trim((string) ($row[$indexes['domain_name']] ?? ''));
                $subdomainName = trim((string) ($row[$indexes['subdomain_name']] ?? ''));

                if ($qualificationName === '' || $domainName === '' || $subdomainName === '') {
                    $skipped++;
                    continue;
                }

                // 資格→分野→サブ分野を順に登録
                $qualification = Qualification::firstOrCreate(
                    ['name' => $qualificationName],
                    ['is_active' => true],
                );

                $domain = QualificationDomain::firstOrCreate(
                    ['qualification_id' => $qualification->qualification_id, 'name' => $domainName],
                    ['is_active' => true],
                );

                $subdomain = QualificationSubdomain::firstOrCreate(
                    ['qualification_domains_id' => $domain->qualification_domains_id, 'name' => $subdomainName],
                    ['is_active' => true],
                );

                // 追加/既存をカウント
                if ($subdomain->wasRecentlyCreated) {
                    $added++;
                } else {
                    $duplicates++;
                }
            }
        } catch (Throwable $exception) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => 'CSVの取り込みに失敗しました',
            ]);
        }

        // 有効な行がない場合のエラー
        if ($added === 0 && $duplicates === 0 && $skipped === 0) {
            return back()->with([
                'toast_status' => 'error',
                'toast_message' => 'CSVに追加対象がありません',
            ]);
        }

        // 取り込み結果を表示
        $message = "追加{$added}件、既存{$duplicates}件、スキップ{$skipped}件";

        return back()->with([
            'toast_status' => 'success',
            'toast_message' => $message,
        ]);
    }
}
