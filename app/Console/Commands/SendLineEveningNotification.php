<?php

namespace App\Console\Commands;

use App\Models\LineAccount;
use App\Services\LineMessagingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendLineEveningNotification extends Command
{
    protected $signature = 'line:send-evening
                            {--time= : 送信時刻のシミュレート（例: 20:00）。省略時は現在時刻で判定}
                            {--user= : 指定したユーザーIDにのみ送信（テスト用。time と併用）}';

    protected $description = 'LINEで夜の通知（累積達成率・入力促進）を送信する';

    public function handle(LineMessagingService $line): int
    {
        $currentTime = $this->option('time') ?? \Carbon\Carbon::now('Asia/Tokyo')->format('H:i');
        $currentTime = $this->normalizeTimeOption($currentTime);

        $query = LineAccount::whereNotNull('line_user_id')
            ->whereHas('user', fn ($q) => $q->whereRaw('line_evening_time::text LIKE ?', [$currentTime . '%'])->where('line_notify_enabled', true));
        if ($userId = $this->option('user')) {
            $query->where('user_id', $userId);
        }
        $accounts = $query->with('user')->get();

        if ($this->option('time')) {
            $this->info("送信時刻を {$currentTime} としてシミュレートしています。");
        }
        if ($accounts->isEmpty()) {
            $this->warn('送信対象のユーザーが0人です。以下を確認してください：');
            $this->warn('  - line_accounts.line_user_id が NULL でない（LINE友だち追加・コード送信済み）');
            $this->warn('  - users.line_notify_enabled が true');
            $this->warn('  - users.line_evening_time が --time= で指定した時刻と一致（例: 20:00 なら --time=20:00）');
        }

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

    /** オプションの時刻を HH:MM に正規化（6:41 → 06:41、0641 → 06:41） */
    private function normalizeTimeOption(?string $time): string
    {
        if ($time === null || $time === '') {
            return \Carbon\Carbon::now('Asia/Tokyo')->format('H:i');
        }
        $time = preg_replace('/\s+/', '', $time);
        $time = preg_replace('/:+/', ':', $time);
        if (strlen($time) === 4 && ctype_digit($time)) {
            return substr($time, 0, 2) . ':' . substr($time, 2, 2);
        }
        if (preg_match('/^(\d{1,2}):(\d{1,2})/', $time, $m)) {
            return sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
        }
        return $time;
    }
}
