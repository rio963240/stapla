<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanRegisterSubdomainRequest;
use App\Models\StudyPlan;
use App\Models\StudyPlanItem;
use App\Models\Todo;
use App\Models\UserNoStudyDay;
use App\Models\UserQualificationTarget;
use App\Models\UserSubdomainPreference;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlanRegisterSubdomainController extends Controller
{
    public function store(PlanRegisterSubdomainRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($request, $validated): JsonResponse {
            $user = $request->user();
            $startDate = Carbon::parse($validated['start_date'])->startOfDay();
            $examDate = Carbon::parse($validated['exam_date'])->startOfDay();
            $endDate = $examDate->copy()->subDay();

            if ($endDate->lt($startDate)) {
                throw ValidationException::withMessages([
                    'exam_date' => ['学習期間が確保できません。'],
                ]);
            }

            $alreadyTargeted = UserQualificationTarget::query()
                ->where('user_id', $user->id)
                ->where('qualification_id', $validated['qualification_id'])
                ->exists();
            if (!$alreadyTargeted) {
                $targetCount = UserQualificationTarget::query()
                    ->where('user_id', $user->id)
                    ->count();
                if ($targetCount >= 3) {
                    throw ValidationException::withMessages([
                        'qualification_id' => ['計画は3件までしか登録できません。'],
                    ]);
                }
            }

            // 目標情報は同一ユーザー×資格で上書き
            $target = UserQualificationTarget::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'qualification_id' => $validated['qualification_id'],
                ],
                [
                    'start_date' => $startDate->toDateString(),
                    'exam_date' => $examDate->toDateString(),
                    'daily_study_time' => $validated['daily_study_time'],
                    'buffer_rate' => $validated['buffer_rate'],
                ],
            );

            // 勉強不可日を一括置き換え
            UserNoStudyDay::where('user_qualification_targets_id', $target->user_qualification_targets_id)->delete();
            $noStudyDays = collect($validated['no_study_days'] ?? [])
                ->filter()
                ->unique()
                ->values();
            foreach ($noStudyDays as $noStudyDay) {
                UserNoStudyDay::create([
                    'user_qualification_targets_id' => $target->user_qualification_targets_id,
                    'no_study_day' => $noStudyDay,
                ]);
            }

            // サブ分野重みを一括置き換え
            UserSubdomainPreference::where('user_qualification_targets_id', $target->user_qualification_targets_id)->delete();
            foreach ($validated['subdomains'] as $subdomain) {
                UserSubdomainPreference::create([
                    'user_qualification_targets_id' => $target->user_qualification_targets_id,
                    'qualification_subdomains_id' => $subdomain['id'],
                    'weight' => $subdomain['weight'],
                ]);
            }

            // 既存計画の最新バージョンを取得
            $latestPlan = StudyPlan::query()
                ->where('user_qualification_targets_id', $target->user_qualification_targets_id)
                ->orderByDesc('version')
                ->first();

            $nextVersion = $latestPlan ? $latestPlan->version + 1 : 1;
            // 既存の有効計画があれば無効化
            if ($latestPlan) {
                StudyPlan::where('user_qualification_targets_id', $target->user_qualification_targets_id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            // 新しい計画を作成
            $studyPlan = StudyPlan::create([
                'user_qualification_targets_id' => $target->user_qualification_targets_id,
                'version' => $nextVersion,
                'is_active' => true,
            ]);

            // 学習可能日（開始日〜試験前日、不可日除外）を作成
            $noStudySet = $noStudyDays->flip();
            $studyDates = [];
            foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
                $dateString = $date->toDateString();
                if ($noStudySet->has($dateString)) {
                    continue;
                }
                $studyDates[] = $date->copy();
            }

            $availableDays = count($studyDates);
            if ($availableDays === 0) {
                throw ValidationException::withMessages([
                    'no_study_days' => ['学習可能日がありません。'],
                ]);
            }

            // 余裕確保率を反映した計画時間
            $totalCapacity = $availableDays * (int) $validated['daily_study_time'];
            $planCapacity = (int) floor($totalCapacity * (1 - ((int) $validated['buffer_rate']) / 100));

            $totalWeight = collect($validated['subdomains'])->sum('weight');

            // サブ分野別の1日あたり学習時間（切り捨て）
            $dailyMinutes = [];
            foreach ($validated['subdomains'] as $subdomain) {
                $allocMinutes = $planCapacity * ($subdomain['weight'] / $totalWeight);
                $dailyMinutes[$subdomain['id']] = (int) floor($allocMinutes / $availableDays);
            }

            // todo と study_plan_items を生成
            $todos = [];
            foreach ($studyDates as $date) {
                $todos[] = Todo::create([
                    'study_plans_id' => $studyPlan->study_plans_id,
                    'date' => $date->toDateString(),
                ]);
            }

            $lastTodo = $todos[$availableDays - 1] ?? null;
            $lastTodoItems = [];
            foreach ($todos as $todo) {
                foreach ($validated['subdomains'] as $subdomain) {
                    $item = StudyPlanItem::create([
                        'todo_id' => $todo->todo_id,
                        'qualification_domains_id' => null,
                        'qualification_subdomains_id' => $subdomain['id'],
                        'planned_minutes' => $dailyMinutes[$subdomain['id']],
                        'status' => false,
                    ]);
                    if ($lastTodo && $todo->todo_id === $lastTodo->todo_id) {
                        $lastTodoItems[$subdomain['id']] = $item;
                    }
                }
            }

            // 端数調整（最終日に+1分ずつ配分）
            $sumPlanned = array_sum($dailyMinutes) * $availableDays;
            $leftover = $planCapacity - $sumPlanned;
            if ($leftover > 0 && $lastTodo) {
                while ($leftover > 0) {
                    foreach ($validated['subdomains'] as $subdomain) {
                        if ($leftover <= 0) {
                            break;
                        }
                        $item = $lastTodoItems[$subdomain['id']] ?? null;
                        if ($item) {
                            $item->increment('planned_minutes');
                            $leftover -= 1;
                        }
                    }
                }
            }

            return response()->json([
                'study_plans_id' => $studyPlan->study_plans_id,
            ]);
        });
    }
}
