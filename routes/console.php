<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto cleanup cache and old logs every 6 hours for smooth running and disk free
Schedule::command('system:cleanup')->everySixHours()->withoutOverlapping()->appendOutputTo(storage_path('logs/cleanup.log'));

// Remove orphaned note PDFs weekly (Sundays 2 AM) — files deleted from DB but still on disk
Schedule::command('notes:cleanup')->weeklyOn(0, '02:00')->withoutOverlapping()->appendOutputTo(storage_path('logs/cleanup.log'));
