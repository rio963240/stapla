<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanRescheduleDataRequest;
use App\Http\Requests\PlanRescheduleStoreRequest;
use App\Models\StudyPlan;
use App\Models\StudyPlanItem;
use App\Models\Todo;
use App\Models\UserDomainPreference;
use App\Models\UserNoStudyDay;
use App\Models\UserSubdomainPreference;
use App\Models\UserQualificationTarget;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlanRescheduleController extends Controller
{
    // リスケ画面表示用の初期データを返す
    public function data(PlanRescheduleDataRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $userId = $request->user()->id;
        $targetId = (int) $validated['target_id'];

        $target = UserQualificationTarget::query()
            ->where('user_qualification_targets_id', $targetId)
            ->where('user_id', $userId)
            ->first();

        if (!$target) {
            return response()->json(['message' => 'not found'], 404);
        }

        $activePlan = StudyPlan::query()
            ->where('user_qualification_targets_id', $targetId)
            ->where('is_active', true)
            ->exists();

        if (!$activePlan) {
            throw ValidationException::withMessages([
                'target_id' => ['有効な計画が見つかりません。'],
            ]);
        }

        // 勉強不可日（チップ表示用）
        $noStudyDays = UserNoStudyDay::query()
            ->where('user_qualification_targets_id', $targetId)
            ->orderBy('no_study_day')
            ->pluck('no_study_day')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->values();

        // サブ分野/分野の重み（どちらか一方だけ使う）
        $subdomainWeights = UserSubdomainPreference::query()
            ->where('user_qualification_targets_id', $targetId)
            ->join(
                'qualification_subdomains',
                'user_subdomain_preferences.qualification_subdomains_id',
                '=',
                'qualification_subdomains.qualification_subdomains_id',
            )
            ->select([
                'user_subdomain_preferences.qualification_subdomains_id as id',
                'qualification_subdomains.name as name',
                'user_subdomain_preferences.weight as weight',
            ])
            ->orderBy('qualification_subdomains.name')
            ->get();

        $domainWeights = UserDomainPreference::query()
            ->where('user_qualification_targets_id', $targetId)
            ->join(
                'qualification_domains',
                'user_domain_preferences.qualification_domains_id',
                '=',
                'qualification_domains.qualification_domains_id',
            )
            ->select([
                'user_domain_preferences.qualification_domains_id as id',
                'qualification_domains.name as name',
                'user_domain_preferences.weight as weight',
            ])
            ->orderBy('qualification_domains.name')
            ->get();

        $useSubdomain = $subdomainWeights->isNotEmpty();
        $weights = $useSubdomain ? $subdomainWeights : $domainWeights;
        $weightType = $useSubdomain ? 'subdomain' : 'domain';

        $qualificationName = DB::table('qualification')
            ->where('qualification_id', $target->qualification_id)
            ->value('name');

        return response()->json([
            'target_id' => $targetId,
            'qualification_name' => $qualificationName,
            'start_date' => Carbon::parse($target->start_date)->toDateString(),
            'exam_date' => Carbon::parse($target->exam_date)->toDateString(),
            'daily_study_time' => (int) $target->daily_study_time,
            'buffer_rate' => (int) $target->buffer_rate,
            'no_study_days' => $noStudyDays,
            'weight_type' => $weightType,
            'weights' => $weights->values(),
        ]);
    }

    public function store(PlanRescheduleStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $userId = $request->user()->id;
        $targetId = (int) $validated['target_id'];

        $target = UserQualificationTarget::query()
            ->where('user_qualification_targets_id', $targetId)
            ->where('user_id', $userId)
            ->first();

        if (!$target) {
            return response()->json(['message' => 'not found'], 404);
        }

        $activePlan = StudyPlan::query()
            ->where('user_qualification_targets_id', $targetId)
            ->where('is_active', true)
            ->first();

        if (!$activePlan) {
            throw ValidationException::withMessages([
                'target_id' => ['有効な計画が見つかりません。'],
            ]);
        }

        return DB::transaction(function () use ($target, $activePlan, $validated): JsonResponse {
            $timezone = 'Asia/Tokyo';
            $baseDate = Carbon::now($timezone)->startOfDay();
            $minStartDate = $baseDate->copy()->addDay();
            $inputStartDate = Carbon::parse($validated['reschedule_start_date'], $timezone)->startOfDay();
            // リスケ開始日は「明日以降」で固定
            $regenFrom = $inputStartDate->gt($minStartDate) ? $inputStartDate : $minStartDate;
            $examDate = Carbon::parse($target->exam_date, $timezone)->startOfDay();
            $regenTo = $examDate->copy()->subDay();

            if ($inputStartDate->lt($minStartDate)) {
                throw ValidationException::withMessages([
                    'reschedule_start_date' => ['リスケ開始日は明日以降で指定してください。'],
                ]);
            }

            if ($inputStartDate->gt($regenTo)) {
                throw ValidationException::withMessages([
                    'reschedule_start_date' => ['リスケ開始日が試験前日を超えています。'],
                ]);
            }

            if ($regenTo->lt($regenFrom)) {
                throw ValidationException::withMessages([
                    'target_id' => ['再配分可能日がありません。'],
                ]);
            }

            // 勉強不可日を上書き保存
            $noStudyDays = collect($validated['no_study_days'] ?? [])
                ->filter()
                ->unique()
                ->values();

            UserNoStudyDay::where('user_qualification_targets_id', $target->user_qualification_targets_id)->delete();
            foreach ($noStudyDays as $noStudyDay) {
                UserNoStudyDay::create([
                    'user_qualification_targets_id' => $target->user_qualification_targets_id,
                    'no_study_day' => $noStudyDay,
                ]);
            }

            $noStudySet = $noStudyDays
                ->map(fn ($date) => Carbon::parse($date, $timezone)->toDateString())
                ->flip();

            $days = [];
            foreach (CarbonPeriod::create($regenFrom, $regenTo) as $date) {
                $dateString = $date->toDateString();
                if ($noStudySet->has($dateString)) {
                    continue;
                }
                $days[] = $date->copy();
            }

            $daysCount = count($days);
            if ($daysCount === 0) {
                throw ValidationException::withMessages([
                    'target_id' => ['再配分可能日がありません。'],
                ]);
            }

            $plannedTotal = (int) DB::table('study_plan_items')
                ->join('todo', 'study_plan_items.todo_id', '=', 'todo.todo_id')
                ->where('todo.study_plans_id', $activePlan->study_plans_id)
                ->sum('study_plan_items.planned_minutes');

            $actualTotal = (int) DB::table('study_records')
                ->join('study_plan_items', 'study_records.study_plan_items_id', '=', 'study_plan_items.study_plan_items_id')
                ->join('todo', 'study_plan_items.todo_id', '=', 'todo.todo_id')
                ->where('todo.study_plans_id', $activePlan->study_plans_id)
                ->sum('study_records.actual_minutes');

            $remainingTotal = max(0, $plannedTotal - $actualTotal);

            // リスケ入力値で目標情報を更新
            $target->daily_study_time = (int) $validated['daily_study_time'];
            $target->buffer_rate = (int) $validated['buffer_rate'];
            $target->save();

            $effectiveDaily = (int) floor($target->daily_study_time * (1 - ($target->buffer_rate) / 100));
            $totalCapacity = $effectiveDaily * $daysCount;
            if ($remainingTotal > $totalCapacity) {
                throw ValidationException::withMessages([
                    'target_id' => ['学習時間が不足しているためリスケジュールできません。'],
                ]);
            }

            $useSubdomain = $validated['weight_type'] === 'subdomain';
            $payloadWeights = collect($validated['weights'] ?? []);

            if ($useSubdomain) {
                $existingIds = UserSubdomainPreference::query()
                    ->where('user_qualification_targets_id', $target->user_qualification_targets_id)
                    ->pluck('qualification_subdomains_id')
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values();
            } else {
                $existingIds = UserDomainPreference::query()
                    ->where('user_qualification_targets_id', $target->user_qualification_targets_id)
                    ->pluck('qualification_domains_id')
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values();
            }

            $payloadIds = $payloadWeights
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();

            if ($existingIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'target_id' => ['分野の重みが設定されていません。'],
                ]);
            }
            if ($existingIds->values()->all() !== $payloadIds->values()->all()) {
                throw ValidationException::withMessages([
                    'weights' => ['分野の追加・削除はできません。'],
                ]);
            }

            // 重みは追加/削除不可、値のみ更新
            if ($useSubdomain) {
                UserSubdomainPreference::where('user_qualification_targets_id', $target->user_qualification_targets_id)->delete();
                foreach ($payloadWeights as $weight) {
                    UserSubdomainPreference::create([
                        'user_qualification_targets_id' => $target->user_qualification_targets_id,
                        'qualification_subdomains_id' => (int) $weight['id'],
                        'weight' => (int) $weight['weight'],
                    ]);
                }
            } else {
                UserDomainPreference::where('user_qualification_targets_id', $target->user_qualification_targets_id)->delete();
                foreach ($payloadWeights as $weight) {
                    UserDomainPreference::create([
                        'user_qualification_targets_id' => $target->user_qualification_targets_id,
                        'qualification_domains_id' => (int) $weight['id'],
                        'weight' => (int) $weight['weight'],
                    ]);
                }
            }

            $weights = $payloadWeights->values();

            $totalWeight = (int) $weights->sum('weight');
            if ($totalWeight <= 0) {
                throw ValidationException::withMessages([
                    'target_id' => ['重みの合計が0です。'],
                ]);
            }

            $domainRemaining = [];
            $domainDaily = [];
            $allocated = 0;
            foreach ($weights as $weight) {
                $id = (int) ($weight['id'] ?? 0);
                $weightValue = (int) ($weight['weight'] ?? 0);
                $portion = (int) floor($remainingTotal * ($weightValue / $totalWeight));
                $domainRemaining[$id] = $portion;
                $allocated += $portion;
                $domainDaily[$id] = (int) floor($effectiveDaily * ($weightValue / $totalWeight));
            }

            $leftover = $remainingTotal - $allocated;
            $domainIds = array_keys($domainRemaining);
            $domainCount = count($domainIds);
            $index = 0;
            while ($leftover > 0 && $domainCount > 0) {
                $domainId = $domainIds[$index % $domainCount];
                $domainRemaining[$domainId] += 1;
                $leftover -= 1;
                $index += 1;
            }

            Todo::query()
                ->where('study_plans_id', $activePlan->study_plans_id)
                ->where('date', '>=', $regenFrom->toDateString())
                ->delete();

            if ($remainingTotal === 0) {
                return response()->json(['study_plans_id' => $activePlan->study_plans_id]);
            }

            $remainingMinutes = $remainingTotal;
            $allocations = [];
            foreach ($days as $date) {
                if ($remainingMinutes <= 0) {
                    break;
                }
                $dateString = $date->toDateString();
                $remainingDailyCapacity = $effectiveDaily;
                $allocations[$dateString] = [];

                foreach ($domainIds as $domainId) {
                    if ($remainingDailyCapacity <= 0) {
                        break;
                    }
                    $domainLeft = $domainRemaining[$domainId] ?? 0;
                    if ($domainLeft <= 0) {
                        continue;
                    }
                    $dailyCap = $domainDaily[$domainId] ?? 0;
                    $allocate = min($dailyCap, $domainLeft, $remainingDailyCapacity);
                    if ($allocate <= 0) {
                        continue;
                    }
                    $allocations[$dateString][$domainId] = ($allocations[$dateString][$domainId] ?? 0) + $allocate;
                    $domainRemaining[$domainId] -= $allocate;
                    $remainingMinutes -= $allocate;
                    $remainingDailyCapacity -= $allocate;
                }

                $roundRobinIndex = 0;
                while ($remainingDailyCapacity > 0 && $remainingMinutes > 0 && $domainCount > 0) {
                    $domainId = $domainIds[$roundRobinIndex % $domainCount];
                    if (($domainRemaining[$domainId] ?? 0) > 0) {
                        $allocations[$dateString][$domainId] = ($allocations[$dateString][$domainId] ?? 0) + 1;
                        $domainRemaining[$domainId] -= 1;
                        $remainingMinutes -= 1;
                        $remainingDailyCapacity -= 1;
                    }
                    $roundRobinIndex += 1;
                }
            }

            if ($remainingMinutes > 0) {
                throw ValidationException::withMessages([
                    'target_id' => ['学習時間が不足しているためリスケジュールできません。'],
                ]);
            }

            foreach ($days as $date) {
                $dateString = $date->toDateString();
                $dayAlloc = $allocations[$dateString] ?? [];
                if (empty($dayAlloc)) {
                    continue;
                }

                $todo = Todo::create([
                    'study_plans_id' => $activePlan->study_plans_id,
                    'date' => $dateString,
                ]);

                foreach ($dayAlloc as $domainId => $minutes) {
                    if ($minutes <= 0) {
                        continue;
                    }
                    StudyPlanItem::create([
                        'todo_id' => $todo->todo_id,
                        'qualification_domains_id' => $useSubdomain ? null : $domainId,
                        'qualification_subdomains_id' => $useSubdomain ? $domainId : null,
                        'planned_minutes' => (int) $minutes,
                        'status' => false,
                    ]);
                }
            }

            return response()->json(['study_plans_id' => $activePlan->study_plans_id]);
        });
    }
}
