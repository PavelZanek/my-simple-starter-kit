<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Livewire\Mechanisms\HandleRouting\LivewirePageController;

Route::middleware(['auth'])->group(function (): void {
    Route::redirect('settings', 'settings/profile');

    $route = Route::get('settings/profile', LivewirePageController::class);
    $route->action['livewire_component'] = 'pages::settings.profile';
    $route->name('profile.edit');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    $route = Route::get('settings/appearance', LivewirePageController::class);
    $route->action['livewire_component'] = 'pages::settings.appearance';
    $route->name('appearance.edit');

    $route = Route::get('settings/security', LivewirePageController::class);
    $route->action['livewire_component'] = 'pages::settings.security';
    $route->middleware(['password.confirm'])
        ->name('security.edit');
});
