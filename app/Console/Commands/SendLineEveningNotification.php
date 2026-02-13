<?php

namespace App\Console\Commands;

use App\Models\LineAccount;
use App\Services\LineMessagingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendLineEveningNotification extends Command
{
    protected $signature = 'line:send-evening';

    protected $description = 'LINEで夜の通知（累積達成率・入力促進）を送信する';

    public function handle(LineMessagingService $line): int
    {
        $currentTime = \Carbon\Carbon::now('Asia/Tokyo')->format('H:i');

        $accounts = LineAccount::whereNotNull('line_user_id')
            ->where('notification_evening_at', $currentTime)
            ->with('user')
            ->get();

        $inputUrl = url('/dashboard');

        foreach ($accounts as $account) {
            $userId = $account->user_id;

            // アクティブな計画を1つ取得
            $target = DB::table('user_qualification_targets as uqt')
                ->join('qualification as q', 'uqt.qualification_id', '=', 'q.qualification_id')
                ->leftJoin('study_plans as sp', function ($join) {
                    $join->on('sp.user_qualification_targets_id', '=', 'uqt.user_qualification_targets_id')
                        ->where('sp.is_active', true);
                })
                ->where('uqt.user_id', $userId)
                ->whereNotNull('sp.study_plans_id')
                ->orderBy('uqt.created_at', 'desc')
                ->select(['sp.study_plans_id as plan_id'])
                ->first();

            $rate = 0;
            if ($target && $target->plan_id) {
                $totals = DB::table('todo')
                    ->join('study_plan_items', 'todo.todo_id', '=', 'study_plan_items.todo_id')
                    ->leftJoin('study_records', function ($join) {
                        $join->on('study_records.study_plan_items_id', '=', 'study_plan_items.study_plan_items_id')
                            ->on('study_records.todo_id', '=', 'todo.todo_id');
                    })
                    ->where('todo.study_plans_id', $target->plan_id)
                    ->selectRaw('SUM(study_plan_items.planned_minutes) as planned, SUM(COALESCE(study_records.actual_minutes, 0)) as actual')
                    ->first();
                $planned = (int) ($totals->planned ?? 0);
                $actual = (int) ($totals->actual ?? 0);
                $rate = $planned > 0 ? round(($actual / $planned) * 100, 0) : 0;
            }

            $body = "🌙 今日の学習はどうでしたか？\n\n"
                . "現在の累積達成率：" . $rate . "%\n\n"
                . "実績を入力するとグラフが更新されます。\n"
                . "→ 入力はこちら\n"
                . $inputUrl;

            $line->pushText($account->line_user_id, $body);
        }

        $this->info('Sent evening notifications to ' . $accounts->count() . ' user(s).');

        return self::SUCCESS;
    }
}
