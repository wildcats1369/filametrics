<?php

use Illuminate\Support\Facades\Route;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;
use wildcats1369\Filametrics\Resources\FilametricsAccountResource;
use wildcats1369\Filametrics\Resources\FilametricsSettingResource;
use Filament\Facades\Filament;

Route::get('/filametrics', function () {
    return 'Filametrics plugin is working!';
});

Route::middleware(['auth', 'web'])
    ->name('filament.admin.resources.')
    ->prefix('/') // Adjust the prefix as needed
    ->group(function () {
        // Retrieve the panel instance
        $panel = Filament::getPanel('filametrics'); // Ensure 'filametrics' matches the panel ID
    
        // Register routes for each resource in the panel
        FilametricsSiteResource::routes($panel);
        FilametricsAccountResource::routes($panel);
        // FilametricsSettingResource::routes($panel);
    });


