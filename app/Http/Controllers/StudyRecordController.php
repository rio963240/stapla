<?php

namespace App\Http\Controllers;

use App\Models\StudyRecord;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudyRecordController extends Controller
{
    public function showTodo(Request $request, int $todoId): JsonResponse
    {
        $userId = $request->user()->id;

        $todo = DB::table('todo')
            ->join('study_plans', 'todo.study_plans_id', '=', 'study_plans.study_plans_id')
            ->join(
                'user_qualification_targets',
                'study_plans.user_qualification_targets_id',
                '=',
                'user_qualification_targets.user_qualification_targets_id',
            )
            ->join('qualification', 'user_qualification_targets.qualification_id', '=', 'qualification.qualification_id')
            ->where('todo.todo_id', $todoId)
            ->where('user_qualification_targets.user_id', $userId)
            ->select([
                'todo.todo_id',
                'todo.date',
                'todo.memo',
                'qualification.name as qualification_name',
            ])
            ->first();

        if (!$todo) {
            return response()->json(['message' => 'not found'], 404);
        }

        $latestRecords = DB::table('study_records')
            ->select('study_plan_items_id', DB::raw('MAX(study_records_id) as latest_id'))
            ->groupBy('study_plan_items_id');

        $items = DB::table('study_plan_items')
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
            ->leftJoinSub($latestRecords, 'latest_records', function ($join) {
                $join->on(
                    'study_plan_items.study_plan_items_id',
                    '=',
                    'latest_records.study_plan_items_id',
                );
            })
            ->leftJoin('study_records as sr', 'sr.study_records_id', '=', 'latest_records.latest_id')
            ->where('study_plan_items.todo_id', $todoId)
            ->select([
                'study_plan_items.study_plan_items_id',
                'study_plan_items.planned_minutes',
                DB::raw('COALESCE(qualification_subdomains.name, qualification_domains.name) as domain_name'),
                'sr.actual_minutes',
            ])
            ->orderBy('study_plan_items.study_plan_items_id')
            ->get();

        return response()->json([
            'todo_id' => $todo->todo_id,
            'date' => $todo->date,
            'memo' => $todo->memo ?? '',
            'qualification_name' => $todo->qualification_name,
            'items' => $items,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'todo_id' => ['required', 'integer', 'exists:todo,todo_id'],
            'memo' => ['nullable', 'string', 'max:2000'],
            'records' => ['required', 'array', 'min:1'],
            'records.*.study_plan_items_id' => ['required', 'integer'],
            'records.*.actual_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
        ]);

        $todoId = (int) $validated['todo_id'];
        $userId = $request->user()->id;

        $todo = Todo::query()
            ->join('study_plans', 'todo.study_plans_id', '=', 'study_plans.study_plans_id')
            ->join(
                'user_qualification_targets',
                'study_plans.user_qualification_targets_id',
                '=',
                'user_qualification_targets.user_qualification_targets_id',
            )
            ->where('todo.todo_id', $todoId)
            ->where('user_qualification_targets.user_id', $userId)
            ->select(['todo.todo_id', 'todo.memo'])
            ->first();

        if (!$todo) {
            return response()->json(['message' => 'not found'], 404);
        }

        $items = DB::table('study_plan_items')
            ->where('todo_id', $todoId)
            ->get(['study_plan_items_id', 'planned_minutes'])
            ->keyBy('study_plan_items_id');

        foreach ($validated['records'] as $record) {
            $itemId = (int) $record['study_plan_items_id'];
            if (!$items->has($itemId)) {
                throw ValidationException::withMessages([
                    'records' => ['対象外の学習項目が含まれています。'],
                ]);
            }
        }

        return DB::transaction(function () use ($validated, $todoId, $items): JsonResponse {
            Todo::where('todo_id', $todoId)->update([
                'memo' => $validated['memo'] ?? null,
            ]);

            foreach ($validated['records'] as $record) {
                $itemId = (int) $record['study_plan_items_id'];
                $actualMinutes = (int) $record['actual_minutes'];
                $plannedMinutes = (int) ($items[$itemId]->planned_minutes ?? 0);

                StudyRecord::updateOrCreate(
                    [
                        'todo_id' => $todoId,
                        'study_plan_items_id' => $itemId,
                    ],
                    [
                        'actual_minutes' => $actualMinutes,
                        'is_completed' => $actualMinutes >= $plannedMinutes,
                    ],
                );
            }

            return response()->json(['todo_id' => $todoId]);
        });
    }
}
