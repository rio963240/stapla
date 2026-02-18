<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// サンプル: 名言を表示
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 自動バックアップを毎分実行（内部で時刻判定）
Schedule::command('backups:auto')
    ->everyMinute()
    ->withoutOverlapping();

// LINE 朝・夜の通知（毎分実行し、現在時刻と一致するユーザーにのみ送信）
Schedule::command('line:send-morning')
    ->everyMinute()
    ->timezone('Asia/Tokyo');
Schedule::command('line:send-evening')
    ->everyMinute()
    ->timezone('Asia/Tokyo');
