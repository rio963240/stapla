<?php

namespace Database\Seeders;

use App\Models\Qualification;
use App\Models\QualificationDomain;
use App\Models\QualificationSubdomain;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class QualificationTemplateSeeder extends Seeder
{
    /**
     * Seed qualification templates from CSV.
     */
    public function run(): void
    {
        // CSVの存在チェック
        $path = base_path('database/seeders/data/qualification_template.csv');
        if (!file_exists($path)) {
            throw new RuntimeException("CSV not found: {$path}");
        }

        // CSVから有効行を読み込み
        $rows = $this->readCsvRows($path);
        if ($rows === []) {
            return;
        }

        // 一括登録はトランザクションで実施
        DB::transaction(function () use ($rows) {
            $now = now();

            $qualificationNames = [];
            $domainByQualification = [];
            $subdomainByQualificationDomain = [];

            // 資格/分野/サブ分野の重複を排除しながら収集
            foreach ($rows as [$qualificationName, $domainName, $subdomainName]) {
                $qualificationNames[$qualificationName] = true;
                $domainByQualification[$qualificationName][$domainName] = true;
                $subdomainByQualificationDomain[$qualificationName . '|' . $domainName][$subdomainName] = true;
            }

            // 資格の一括登録（既存は更新）
            $qualificationNames = array_keys($qualificationNames);
            $qualificationRows = array_map(
                fn ($name) => [
                    'name' => $name,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                $qualificationNames
            );

            Qualification::upsert($qualificationRows, ['name'], ['is_active', 'updated_at']);

            // 資格名 → ID のマップ作成
            $qualificationMap = Qualification::query()
                ->whereIn('name', $qualificationNames)
                ->get()
                ->keyBy('name');

            // 分野の一括登録データ作成
            $domainRows = [];
            foreach ($domainByQualification as $qualificationName => $domainNames) {
                $qualification = $qualificationMap->get($qualificationName);
                if ($qualification === null) {
                    continue;
                }

                foreach (array_keys($domainNames) as $domainName) {
                    $domainRows[] = [
                        'qualification_id' => $qualification->qualification_id,
                        'name' => $domainName,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // 分野の一括登録（既存は更新）
            if ($domainRows !== []) {
                QualificationDomain::upsert(
                    $domainRows,
                    ['qualification_id', 'name'],
                    ['is_active', 'updated_at']
                );
            }

            // 資格IDと分野名から分野IDを引けるようにマップ化
            $qualificationIds = $qualificationMap->pluck('qualification_id')->all();
            $domainMap = QualificationDomain::query()
                ->whereIn('qualification_id', $qualificationIds)
                ->get()
                ->mapWithKeys(
                    fn ($domain) => [
                        $domain->qualification_id . '|' . $domain->name => $domain->qualification_domains_id,
                    ]
                );

            // サブ分野の一括登録データ作成
            $subdomainRows = [];
            foreach ($subdomainByQualificationDomain as $qualificationDomainKey => $subdomainNames) {
                [$qualificationName, $domainName] = explode('|', $qualificationDomainKey, 2);
                $qualification = $qualificationMap->get($qualificationName);
                if ($qualification === null) {
                    continue;
                }

                $domainId = $domainMap->get($qualification->qualification_id . '|' . $domainName);
                if ($domainId === null) {
                    continue;
                }

                foreach (array_keys($subdomainNames) as $subdomainName) {
                    $subdomainRows[] = [
                        'qualification_domains_id' => $domainId,
                        'name' => $subdomainName,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // サブ分野の一括登録（既存は更新）
            if ($subdomainRows !== []) {
                QualificationSubdomain::upsert(
                    $subdomainRows,
                    ['qualification_domains_id', 'name'],
                    ['is_active', 'updated_at']
                );
            }
        });
    }

    /**
     * @return array<int, array{0:string,1:string,2:string}>
     */
    private function readCsvRows(string $path): array
    {
        // CSVを読み込み、有効な行だけを抽出
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException("Failed to open CSV: {$path}");
        }

        $rows = [];
        $isHeader = true;

        while (($data = fgetcsv($handle)) !== false) {
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            if (count($data) < 3) {
                continue;
            }

            $qualificationName = trim($data[0]);
            $domainName = trim($data[1]);
            $subdomainName = trim($data[2]);

            if ($qualificationName === '' || $domainName === '' || $subdomainName === '') {
                continue;
            }

            $rows[] = [$qualificationName, $domainName, $subdomainName];
        }

        fclose($handle);

        return $rows;
    }
}
