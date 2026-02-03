<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function events(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
            'view' => ['nullable', 'string'],
        ]);

        // ビュー種別で月/週の返却内容を切り替える
        $userId = $request->user()->id;
        $start = Carbon::parse($request->query('start'))->startOfDay();
        $end = Carbon::parse($request->query('end'))->startOfDay();
        $viewType = (string) $request->query('view', 'dayGridMonth');
        $isMonthView = $viewType === 'dayGridMonth';

        if ($isMonthView) {
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
                ])
                ->get();

            $events = $rows->map(fn ($row) => [
                'id' => 'todo-' . $row->todo_id,
                'title' => $row->qualification_name,
                'start' => $row->date,
                'allDay' => true,
                'extendedProps' => [
                    'qualificationName' => $row->qualification_name,
                ],
            ]);

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
            ->where('study_plans.is_active', true)
            ->where('user_qualification_targets.user_id', $userId)
            ->where('todo.date', '>=', $start->toDateString())
            ->where('todo.date', '<', $end->toDateString())
            ->select([
                'study_plan_items.study_plan_items_id',
                'todo.date',
                'qualification.name as qualification_name',
                'qualification_domains.name as domain_name',
                'study_plan_items.planned_minutes',
            ])
            ->get();

        $events = $rows
            ->groupBy('date')
            ->map(function ($items, $date) {
                $first = $items->first();
                $details = $items->map(function ($item) {
                    return [
                        'domainName' => $item->domain_name,
                        'plannedMinutes' => (int) $item->planned_minutes,
                    ];
                })->values();

                return [
                    'id' => 'todo-' . $date,
                    'title' => $first->qualification_name,
                    'start' => $date,
                    'allDay' => true,
                    'extendedProps' => [
                        'qualificationName' => $first->qualification_name,
                        'details' => $details,
                    ],
                ];
            })
            ->values();

        return response()->json($events);
    }
}
