<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\RefreshCoinsList;
use Illuminate\Support\Facades\Auth;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/*
Artisan::command('refresh:coins', function () {
    dispatch(new RefreshCoinsList(Auth::id()));  
})->purpose('Refreshing coins datas')->everyTenMinutes();
*/

