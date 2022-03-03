<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('project:init', function () {
    Artisan::call('migrate:fresh');
    Artisan::call('passport:install');
    Artisan::call('vendor:publish');
    Artisan::call('optimize:clear');
})->purpose('Setup project');
