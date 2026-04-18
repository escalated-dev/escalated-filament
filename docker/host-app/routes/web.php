<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (app()->environment('demo')) {
        return redirect()->route('demo.picker');
    }

    return response('Escalated Filament demo host. Set APP_ENV=demo to enable the click-to-login page.', 200);
});
