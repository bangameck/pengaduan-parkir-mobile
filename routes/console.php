<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ## TAMBAHKAN KODE DI BAWAH INI ##
// Menjadwalkan perintah pembersihan file temporary untuk berjalan setiap hari.
Schedule::command('app:clean-temporary-files')->daily();
