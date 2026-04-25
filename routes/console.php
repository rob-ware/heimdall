<?php

use App\Console\Commands\CsrfCheck;
use App\Console\Commands\ScriptInjectionCheck;
use App\Console\Commands\SqlInjectionCheck;
use App\Console\Commands\MonitorEnvFile;
use App\Console\Commands\MonitorLogins;
use App\Console\Commands\MonitorRootAccess;
use App\Console\Commands\ServerLoad;
use App\Console\Commands\IntrusionDetectionSystem;
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
Schedule::command('app:monitor-root-access')->everyMinute();
Schedule::command('app:server-load')->everyThirtySeconds();
Schedule::command('app:intrusion-detection-system')->everyFifteenSeconds();
