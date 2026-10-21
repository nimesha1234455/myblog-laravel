<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Weekly summary email to admins, every Monday at 8:00 AM (Sri Lanka time)
Schedule::command('myblog:weekly')
    ->weeklyOn(1, '08:00')
    ->timezone('Asia/Colombo');

// Delete activity logs older than 30 days, every day at 2:00 AM (Sri Lanka time)
Schedule::command('activity:prune --days=30')
    ->dailyAt('02:00')
    ->timezone('Asia/Colombo');