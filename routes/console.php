<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

//Schedule::command('app:send-notification')->everyThirtyMinutes();
Schedule::command('app:send-notification')->everyMinute();
