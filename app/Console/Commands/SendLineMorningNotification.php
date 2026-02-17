<?php

namespace App\Console\Commands;

use App\Models\LineAccount;
use App\Services\LineMessagingService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendLineMorningNotification extends Command
{
    protected $signature = 'line:send-morning
                            {--time= : 送信時刻のシミュレート（例: 08:00）。省略時は現在時刻で判定}
                            {--user= : 指定したユーザーIDにのみ送信（テスト用。time と併用）}';

    protected $description = 'LINEで朝の通知（本日の予定）を送信する';

    public function handle(LineMessagingService $line): int
    {
        $today = Carbon::today()->toDateString();
        $currentTime = $this->option('time') ?? Carbon::now('Asia/Tokyo')->format('H:i');
        $currentTime = $this->normalizeTimeOption($currentTime);

        $query = LineAccount::whereNotNull('line_user_id')
            ->whereHas('user', fn ($q) => $q->whereRaw('line_morning_time::text LIKE ?', [$currentTime . '%'])->where('line_notify_enabled', true));
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
            $this->warn('  - users.line_morning_time が --time= で指定した時刻と一致（例: 08:00 なら --time=08:00）');
            $this->warn('  - 本番の場合: cron で php artisan schedule:run が毎分実行されているか');
        }

        foreach ($accounts as $account) {
            $userId = $account->user_id;

            // 勉強不可日かどうか（いずれかの資格目標で今日が登録されていればお休みの日）
            $isNoStudyDay = DB::table('user_no_study_days')
                ->join('user_qualification_targets', 'user_no_study_days.user_qualification_targets_id', '=', 'user_qualification_targets.user_qualification_targets_id')
                ->where('user_qualification_targets.user_id', $userId)
                ->where('user_no_study_days.no_study_day', $today)
                ->exists();

            if ($isNoStudyDay) {
                $body = "☀️ おはようございます！\n今日はお休みの日。ゆっくり過ごしましょう。";
                $line->pushText($account->line_user_id, $body);
                continue;
            }

            $planRows = DB::table('todo')
                ->join('study_plans', 'todo.study_plans_id', '=', 'study_plans.study_plans_id')
                ->join(
                    'user_qualification_targets',
                    'study_plans.user_qualification_targets_id',
                    '=',
                    'user_qualification_targets.user_qualification_targets_id',
                )
                ->join('study_plan_items', 'todo.todo_id', '=', 'study_plan_items.todo_id')
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
                ->where('todo.date', $today)
                ->select([
                    DB::raw('COALESCE(qualification_subdomains.name, qualification_domains.name) as domain_name'),
                    'study_plan_items.planned_minutes',
                ])
                ->orderBy('domain_name')
                ->get();

            $totalMinutes = $planRows->sum('planned_minutes');
            $lines = $planRows->map(fn ($row) => '・' . ($row->domain_name ?? '未分類') . ' ' . (int) $row->planned_minutes . '分');

            $body = "☀️ おはようございます！\n\n"
                . "本日の予定：" . (int) $totalMinutes . "分\n"
                . ($lines->isNotEmpty() ? $lines->implode("\n") . "\n\n" : "\n")
                . "今日も一歩前進しましょう！";

            $line->pushText($account->line_user_id, $body);
        }

        $this->info('Sent morning notifications to ' . $accounts->count() . ' user(s).');

        return self::SUCCESS;
    }

    /** オプションの時刻を HH:MM に正規化（6:41 → 06:41、0641 → 06:41） */
    private function normalizeTimeOption(?string $time): string
    {
        if ($time === null || $time === '') {
            return Carbon::now('Asia/Tokyo')->format('H:i');
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
