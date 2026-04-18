<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/demo', function () {
    abort_unless(app()->environment('demo'), 404);

    // Filament demo is agent-facing only — customers don't have a Filament UI.
    $users = User::where('is_admin', true)
        ->orWhere('is_agent', true)
        ->orderByDesc('is_admin')
        ->orderByDesc('is_agent')
        ->orderBy('id')
        ->get(['id', 'name', 'email', 'is_admin', 'is_agent']);

    return view('demo.picker', ['users' => $users]);
})->name('demo.picker');

Route::post('/demo/login/{user}', function (User $user) {
    abort_unless(app()->environment('demo'), 404);

    Auth::login($user);
    request()->session()->regenerate();

    return redirect('/admin');
})->name('demo.login');

Route::post('/demo/logout', function () {
    abort_unless(app()->environment('demo'), 404);

    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('demo.picker');
})->name('demo.logout');
