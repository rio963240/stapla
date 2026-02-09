<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalendarEventsRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function events(CalendarEventsRequest $request): JsonResponse
    {
        // 入力パラメータのバリデーション
        $request->validated();

        // ビュー種別で月/週の返却内容を切り替えるための基礎情報を準備
        $userId = $request->user()->id;
        $start = Carbon::parse($request->query('start'))->startOfDay();
        $end = Carbon::parse($request->query('end'))->startOfDay();
        $viewType = (string) $request->query('view', 'dayGridMonth');
        $isMonthView = $viewType === 'dayGridMonth';
        $palette = [
            '#3ab679', // 1つ目
            '#f59e0b', // 2つ目
            '#c4b5fd', // 3つ目
        ];

        // 勉強不可日の取得（連続イベントの分割に使用）
        $noStudyRows = DB::table('user_no_study_days')
            ->join(
                'user_qualification_targets',
                'user_no_study_days.user_qualification_targets_id',
                '=',
                'user_qualification_targets.user_qualification_targets_id',
            )
            ->where('user_qualification_targets.user_id', $userId)
            ->where('user_no_study_days.no_study_day', '>=', $start->toDateString())
            ->where('user_no_study_days.no_study_day', '<', $end->toDateString())
            ->select(['user_no_study_days.no_study_day'])
            ->get();
        $noStudySet = $noStudyRows
            ->pluck('no_study_day')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->flip();

        if ($isMonthView) {
            // 月表示：資格ごとの勉強日をまとめて横棒イベントに変換
            $rows = DB::table('todo')
                ->join('study_plans', 'todo.study_plans_id', '=', 'study_plans.study_plans_id')
                ->join(
                    'user_qualification_targets',
                    'study_plans.user_qualification_targets_id',
                    '=',
                    'user_qualification_targets.user_qualification_targets_id',
                )
                ->join('qualification', 'user_qualification_targets.qualification_id', '=', 'qualification.qualification_id')
                ->where('study_plans.is_active', true)
                ->where('user_qualification_targets.user_id', $userId)
                ->where('todo.date', '>=', $start->toDateString())
                ->where('todo.date', '<', $end->toDateString())
                ->select([
                    'todo.todo_id',
                    'todo.date',
                    'qualification.name as qualification_name',
                    'study_plans.study_plans_id as plan_order',
                ])
                ->get();

            $events = collect();
            $grouped = $rows->groupBy('qualification_name');
            $qualificationOrder = $grouped->keys()->values();
            $colorMap = $qualificationOrder
                ->mapWithKeys(function ($name, $index) use ($palette) {
                    $color = $palette[$index % count($palette)];
                    return [$name => $color];
                });
            foreach ($grouped as $qualificationName => $items) {
                // 資格ごとに連続日をまとめる
                $planOrder = $items->min('plan_order') ?? 0;
                $dates = $items
                    ->pluck('date')
                    ->map(fn ($date) => Carbon::parse($date)->toDateString())
                    ->unique()
                    ->sort()
                    ->values();

                if ($dates->isEmpty()) {
                    continue;
                }

                $todoSet = $dates->flip();
                $minDate = Carbon::parse($dates->first())->startOfDay();
                $maxDate = Carbon::parse($dates->last())->startOfDay();

                $rangeStart = null;
                foreach (CarbonPeriod::create($minDate, $maxDate) as $date) {
                    $dateString = $date->toDateString();
                    $isStudyDay = $todoSet->has($dateString);
                    $isNoStudyDay = $noStudySet->has($dateString);

                    // 学習日が連続している区間だけをイベント化する
                    if ($isStudyDay && !$isNoStudyDay) {
                        if (!$rangeStart) {
                            $rangeStart = $date->copy();
                        }
                        continue;
                    }

                    if ($rangeStart) {
                        $events->push([
                            'id' => 'todo-' . $qualificationName . '-' . $rangeStart->toDateString(),
                            'title' => $qualificationName,
                            'start' => $rangeStart->toDateString(),
                            'end' => $dateString,
                            'allDay' => true,
                            'planOrder' => $planOrder,
                            'backgroundColor' => $colorMap[$qualificationName] ?? $palette[0],
                            'borderColor' => $colorMap[$qualificationName] ?? $palette[0],
                            'extendedProps' => [
                                'qualificationName' => $qualificationName,
                            ],
                        ]);
                        $rangeStart = null;
                    }
                }

                if ($rangeStart) {
                    // 末尾まで連続していた区間をイベント化
                    $events->push([
                        'id' => 'todo-' . $qualificationName . '-' . $rangeStart->toDateString(),
                        'title' => $qualificationName,
                        'start' => $rangeStart->toDateString(),
                        'end' => $maxDate->copy()->addDay()->toDateString(),
                        'allDay' => true,
                        'planOrder' => $planOrder,
                        'backgroundColor' => $colorMap[$qualificationName] ?? $palette[0],
                        'borderColor' => $colorMap[$qualificationName] ?? $palette[0],
                        'extendedProps' => [
                            'qualificationName' => $qualificationName,
                        ],
                    ]);
                }
            }

            return response()->json($events);
        }

        // 週表示は分野・分数を1日内で1枚にまとめる
        $rows = DB::table('study_plan_items')
            ->join('todo', 'study_plan_items.todo_id', '=', 'todo.todo_id')
            ->join('study_plans', 'todo.study_plans_id', '=', 'study_plans.study_plans_id')
            ->join(
                'user_qualification_targets',
                'study_plans.user_qualification_targets_id',
                '=',
                'user_qualification_targets.user_qualification_targets_id',
            )
            ->join('qualification', 'user_qualification_targets.qualification_id', '=', 'qualification.qualification_id')
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
            ->where('study_plans.is_active', true)
            ->where('user_qualification_targets.user_id', $userId)
            ->where('todo.date', '>=', $start->toDateString())
            ->where('todo.date', '<', $end->toDateString())
            ->select([
                'study_plan_items.study_plan_items_id',
                'todo.todo_id',
                'todo.date',
                'qualification.name as qualification_name',
                DB::raw('COALESCE(qualification_subdomains.name, qualification_domains.name) as domain_name'),
                'study_plan_items.planned_minutes',
                'study_plans.study_plans_id as plan_order',
            ])
            ->get();

        // 資格名の出現順で色を固定
        $qualificationOrder = $rows->pluck('qualification_name')->unique()->values();
        $colorMap = $qualificationOrder
            ->mapWithKeys(function ($name, $index) use ($palette) {
                $color = $palette[$index % count($palette)];
                return [$name => $color];
            });

        // 日付×資格でまとめて、分野別内訳を付与
        $events = $rows
            ->groupBy(fn ($row) => $row->date . '|' . $row->qualification_name)
            ->map(function ($items, $key) use ($colorMap, $palette) {
                [$date, $qualificationName] = explode('|', $key, 2);
                $planOrder = $items->min('plan_order') ?? 0;
                $todoId = $items->first()->todo_id ?? null;
                $details = $items->map(function ($item) {
                    return [
                        'domainName' => $item->domain_name,
                        'plannedMinutes' => (int) $item->planned_minutes,
                    ];
                })->values();

                return [
                    'id' => 'todo-' . $qualificationName . '-' . $date,
                    'title' => $qualificationName,
                    'start' => $date,
                    'allDay' => true,
                    'planOrder' => $planOrder,
                    'backgroundColor' => $colorMap[$qualificationName] ?? $palette[0],
                    'borderColor' => $colorMap[$qualificationName] ?? $palette[0],
                    'extendedProps' => [
                        'qualificationName' => $qualificationName,
                        'details' => $details,
                        'todoId' => $todoId,
                    ],
                ];
            })
            ->values();

        return response()->json($events);
    }
}
