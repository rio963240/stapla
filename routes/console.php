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

// LINE 朝・夜の通知（時刻は必要に応じて変更）
Schedule::command('line:send-morning')
    ->dailyAt('07:00')
    ->timezone('Asia/Tokyo');
Schedule::command('line:send-evening')
    ->dailyAt('21:00')
    ->timezone('Asia/Tokyo');
