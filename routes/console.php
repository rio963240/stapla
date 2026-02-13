<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('backups:auto')
    ->everyMinute()
    ->withoutOverlapping();

// LINE 朝・夜の通知（毎時0分に実行し、各ユーザーの設定時刻に一致する人に送信）
Schedule::command('line:send-morning')
    ->hourly()
    ->timezone('Asia/Tokyo');
Schedule::command('line:send-evening')
    ->hourly()
    ->timezone('Asia/Tokyo');
