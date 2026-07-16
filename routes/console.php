<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Flush buffered post view counts to the database every 5 minutes.
// Ensure the server cron runs: * * * * * php /path/to/artisan schedule:run
Schedule::command('posts:flush-views')->everyFiveMinutes();
