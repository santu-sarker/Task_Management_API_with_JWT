<?php

use App\Jobs\SendPendingTaskReminder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/** dispatching the SendPendingTaskReminder everyday at 8PM */
Schedule::job(new SendPendingTaskReminder)->dailyAt('20:00');
