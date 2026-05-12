<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Her gece saat 02:00'de eski yedekleri temizle (Sistemde şişmeyi önler)
Schedule::command('backup:clean')
    ->dailyAt('02:00')
    ->monitorName('Eski Yedekleri Temizleme (Clean)');

// Her gece saat 03:00'te veritabanı ve dosya yedeğini al
Schedule::command('backup:run')
    ->dailyAt('03:00')
    ->monitorName('Genel Sistem Yedeği (Backup)');
