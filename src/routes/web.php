<?php

use Illuminate\Support\Facades\Route;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;
use wildcats1369\Filametrics\Resources\FilametricsAccountResource;
use wildcats1369\Filametrics\Resources\FilametricsSettingResource;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages\PdfFilametricSite;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages\PredictFilametricsSite;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


Route::get('/filametrics', function () {
    return 'Filametrics plugin is working!';
});

Route::middleware(['auth', 'web'])
    ->name('filament.admin.resources.')
    ->prefix(config('filament.path', '/')) // Adjust the prefix as needed
    ->group(function () {
        // Retrieve the panel instance
        $panel = Filament::getPanel('filametrics'); // Ensure 'filametrics' matches the panel ID
    
        // Register routes for each resource in the panel
        FilametricsSiteResource::routes($panel);
        FilametricsAccountResource::routes($panel);

        // Add your custom route here:
        Route::get('/filametrics-sites/{record}/predict', PredictFilametricsSite::class)
            ->name('filametrics-sites.predict');

    });

// Route::get('/filament/login-as/{encrypted}', function ($encrypted) {
//     try {
//         $userId = Crypt::decryptString($encrypted);
//         $user = User::findOrFail($userId);
//         Auth::login($user);
//         return redirect(request('redirect', '/admin'));
//     } catch (\Exception $e) {
//         abort(403);
//     }
// })->name('filament.encrypted-login');


// Route::get('/filametrics-sites/{record}/pdf', PdfFilametricSite::class)
//     ->name('filament.admin.resources.filametrics-sites.pdf')
//     ->middleware(['web']); // no auth!

// PDF export route without auth
// Route::middleware(['web'])
//     ->get('/filametrics-sites/{record}/pdf', [PdfFilametricSite::class, 'show'])
//     ->name('filametrics.site.pdf');




// Filament::getPanel('filametrics')->routes(function () {
//     Route::get('/filametrics-sites/{record}/pdf', PdfFilametricSite::class)
//         ->name('filametrics.site.pdf')
//         ->middleware([
//             \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
//             \Filament\Http\Middleware\SetUpPanel::class,
//             \Filament\Http\Middleware\BootstrapPanels::class,
//         ]);
// });


Route::get('/filametrics-sites/{record}/pdf', function ($recordId) {
    $record = \wildcats1369\Filametrics\Models\FilametricsSite::findOrFail($recordId);
    $page = new PdfFilametricSite();
    $page->record = $record;
    $data = $page->getViewData();

    return view('filametrics::pages.pdf-view', $data);
})->name('filament.admin.resources.filametrics-sites.pdf');
