<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:csrf-check')->daily();
Schedule::command('app:script-injection-check')->daily();
Schedule::command('app:sql-injection-check')->daily();


Schedule::command('app:monitor-env-file')->everyThreeMinutes();
Schedule::command('app:monitor-logins')->everyTwoMinutes();
Schedule::command('app:intrusion-detection-system')->everyTwoMinutes();
Schedule::command('app:monitor-root-access')->everyMinute();
Schedule::command('app:server-load')->everyThirtySeconds();

