<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudyProgressController extends Controller
{
    public function index(Request $request)
    {
        // 画面初期表示用の対象取得とデータ組み立て
        $userId = $request->user()->id;
        $targets = $this->fetchTargets($userId);
        $selectedTarget = $this->selectTarget($targets, $request->query('target_id'));

        if (!$selectedTarget) {
            return view('study-progress', [
                'targets' => collect(),
                'initialData' => $this->emptyData(),
            ]);
        }

        [$periodStart, $periodEnd] = $this->resolvePeriod(
            $request->query('start'),
            $request->query('end'),
            $selectedTarget,
        );

        // 進捗グラフ・一覧に必要な集計データを作成
        $data = $this->buildData((int) $selectedTarget->active_plan_id, $periodStart, $periodEnd);
        $data['selected_target_id'] = (int) $selectedTarget->user_qualification_targets_id;
        $data['period_start'] = $periodStart->toDateString();
        $data['period_end'] = $periodEnd->toDateString();
        $data['qualification_name'] = $selectedTarget->qualification_name;

        return view('study-progress', [
            'targets' => $targets,
            'initialData' => $data,
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        // フロントの再取得用API（期間や対象変更時）
        $validated = $request->validate([
            'target_id' => ['required', 'integer'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
        ]);

        $userId = $request->user()->id;
        $targets = $this->fetchTargets($userId);
        $selectedTarget = $this->selectTarget($targets, $validated['target_id'] ?? null);

        if (!$selectedTarget) {
            return response()->json(['message' => 'not found'], 404);
        }

        [$periodStart, $periodEnd] = $this->resolvePeriod(
            $validated['start'] ?? null,
            $validated['end'] ?? null,
            $selectedTarget,
        );

        // 対象プランの集計データを返却
        $data = $this->buildData((int) $selectedTarget->active_plan_id, $periodStart, $periodEnd);
        $data['selected_target_id'] = (int) $selectedTarget->user_qualification_targets_id;
        $data['period_start'] = $periodStart->toDateString();
        $data['period_end'] = $periodEnd->toDateString();
        $data['qualification_name'] = $selectedTarget->qualification_name;

        return response()->json($data);
    }

    private function fetchTargets(int $userId): Collection
    {
        // ユーザーの資格目標一覧とアクティブ計画を取得
        return DB::table('user_qualification_targets as uqt')
            ->join('qualification as q', 'uqt.qualification_id', '=', 'q.qualification_id')
            ->leftJoin('study_plans as sp', function ($join) {
                $join->on('sp.user_qualification_targets_id', '=', 'uqt.user_qualification_targets_id')
                    ->where('sp.is_active', true);
            })
            ->where('uqt.user_id', $userId)
            ->orderBy('uqt.created_at', 'desc')
            ->select([
                'uqt.user_qualification_targets_id',
                'uqt.start_date',
                'uqt.exam_date',
                'q.name as qualification_name',
                'sp.study_plans_id as active_plan_id',
            ])
            ->get();
    }

    private function selectTarget(Collection $targets, $targetId)
    {
        // 指定IDがあれば優先、それ以外はアクティブ計画 → 先頭を選択
        $targetId = $targetId ? (int) $targetId : null;
        if ($targetId) {
            $match = $targets->firstWhere('user_qualification_targets_id', $targetId);
            if ($match) {
                return $match;
            }
        }

        $active = $targets->first(function ($item) {
            return !is_null($item->active_plan_id);
        });
        return $active ?: $targets->first();
    }

    private function resolvePeriod(?string $start, ?string $end, $target): array
    {
        // 期間のデフォルトを対象の開始日〜試験日（最大で今日まで）に設定
        $today = Carbon::today();
        $defaultStart = $target?->start_date ? Carbon::parse($target->start_date)->startOfDay() : $today->copy()->subDays(13);
        $defaultEnd = $target?->exam_date ? Carbon::parse($target->exam_date)->startOfDay() : $today->copy();

        if ($defaultEnd->gt($today)) {
            $defaultEnd = $today->copy();
        }

        $periodStart = $start ? Carbon::parse($start)->startOfDay() : $defaultStart;
        $periodEnd = $end ? Carbon::parse($end)->startOfDay() : $defaultEnd;

        if ($periodEnd->lt($periodStart)) {
            $periodEnd = $periodStart->copy();
        }

        return [$periodStart, $periodEnd];
    }

    private function buildData(int $planId, Carbon $periodStart, Carbon $periodEnd): array
    {
        // 計画IDがない場合は空データを返す
        if (!$planId) {
            return $this->emptyData();
        }

        // 日別の予定/実績合計を取得
        $dailyRows = DB::table('todo')
            ->join('study_plan_items', 'todo.todo_id', '=', 'study_plan_items.todo_id')
            ->leftJoin('study_records', function ($join) {
                $join->on('study_records.study_plan_items_id', '=', 'study_plan_items.study_plan_items_id')
                    ->on('study_records.todo_id', '=', 'todo.todo_id');
            })
            ->where('todo.study_plans_id', $planId)
            ->select([
                'todo.date',
                DB::raw('SUM(study_plan_items.planned_minutes) as planned_minutes'),
                DB::raw('SUM(COALESCE(study_records.actual_minutes, 0)) as actual_minutes'),
            ])
            ->groupBy('todo.date')
            ->orderBy('todo.date')
            ->get();

        $plannedCumulative = 0;
        $actualCumulative = 0;
        // 累積推移データを作成
        $cumulative = $dailyRows->map(function ($row) use (&$plannedCumulative, &$actualCumulative) {
            $plannedCumulative += (int) $row->planned_minutes;
            $actualCumulative += (int) $row->actual_minutes;

            return [
                'date' => Carbon::parse($row->date)->toDateString(),
                'planned_cumulative' => $plannedCumulative,
                'actual_cumulative' => $actualCumulative,
            ];
        })->values();

        // 分野別の予定/実績を集計
        $domainRows = DB::table('study_plan_items')
            ->join('todo', 'study_plan_items.todo_id', '=', 'todo.todo_id')
            ->leftJoin('study_records', function ($join) {
                $join->on('study_records.study_plan_items_id', '=', 'study_plan_items.study_plan_items_id')
                    ->on('study_records.todo_id', '=', 'todo.todo_id');
            })
            ->leftJoin(
                'qualification_domains',
                'study_plan_items.qualification_domains_id',
                '=',
                'qualification_domains.qualification_domains_id',
            )
            ->leftJoin(
                'qualification_subdomains',
                'study_plan_items.qualification_subdomains_id',
                '=',
                'qualification_subdomains.qualification_subdomains_id',
            )
            ->where('todo.study_plans_id', $planId)
            ->select([
                DB::raw('COALESCE(qualification_subdomains.name, qualification_domains.name) as domain_name'),
                DB::raw('SUM(study_plan_items.planned_minutes) as planned_minutes'),
                DB::raw('SUM(COALESCE(study_records.actual_minutes, 0)) as actual_minutes'),
            ])
            ->groupBy('domain_name')
            ->orderBy('domain_name')
            ->get();

        $domainRates = $domainRows->map(function ($row) {
            $planned = (int) $row->planned_minutes;
            $actual = (int) $row->actual_minutes;
            $rate = $planned > 0 ? round(($actual / $planned) * 100, 1) : 0;

            return [
                'name' => $row->domain_name ?? '未分類',
                'planned_minutes' => $planned,
                'actual_minutes' => $actual,
                'achievement_rate' => $rate,
            ];
        })->values();

        // 期間内の日別達成率を集計
        $periodRows = DB::table('todo')
            ->join('study_plan_items', 'todo.todo_id', '=', 'study_plan_items.todo_id')
            ->leftJoin('study_records', function ($join) {
                $join->on('study_records.study_plan_items_id', '=', 'study_plan_items.study_plan_items_id')
                    ->on('study_records.todo_id', '=', 'todo.todo_id');
            })
            ->where('todo.study_plans_id', $planId)
            ->where('todo.date', '>=', $periodStart->toDateString())
            ->where('todo.date', '<=', $periodEnd->toDateString())
            ->select([
                'todo.date',
                DB::raw('SUM(study_plan_items.planned_minutes) as planned_minutes'),
                DB::raw('SUM(COALESCE(study_records.actual_minutes, 0)) as actual_minutes'),
            ])
            ->groupBy('todo.date')
            ->orderBy('todo.date')
            ->get();

        $periodRates = $periodRows->map(function ($row) {
            $planned = (int) $row->planned_minutes;
            $actual = (int) $row->actual_minutes;
            $rate = $planned > 0 ? round(($actual / $planned) * 100, 1) : 0;

            return [
                'date' => Carbon::parse($row->date)->toDateString(),
                'planned_minutes' => $planned,
                'actual_minutes' => $actual,
                'achievement_rate' => $rate,
            ];
        })->values();

        // 全期間の合計と達成率を算出
        $totals = $dailyRows->reduce(function ($carry, $row) {
            $carry['planned'] += (int) $row->planned_minutes;
            $carry['actual'] += (int) $row->actual_minutes;
            return $carry;
        }, ['planned' => 0, 'actual' => 0]);

        $overallRate = $totals['planned'] > 0 ? round(($totals['actual'] / $totals['planned']) * 100, 1) : 0;

        return [
            'cumulative' => $cumulative,
            'domain_rates' => $domainRates,
            'period_rates' => $periodRates,
            'summary' => [
                'planned_total' => $totals['planned'],
                'actual_total' => $totals['actual'],
                'achievement_rate' => $overallRate,
            ],
        ];
    }

    private function emptyData(): array
    {
        return [
            'cumulative' => [],
            'domain_rates' => [],
            'period_rates' => [],
            'summary' => [
                'planned_total' => 0,
                'actual_total' => 0,
                'achievement_rate' => 0,
            ],
            'selected_target_id' => null,
            'period_start' => null,
            'period_end' => null,
            'qualification_name' => null,
        ];
    }
}
