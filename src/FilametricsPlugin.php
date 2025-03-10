<?php
namespace wildcats1369\Filametrics;

use Filament\Contracts\Plugin;
use Filament\Panel;
use wildcats1369\Filametrics\Resources;

class FilametricsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filametrics';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            Resources\FilametricsSiteResource::class,
            Resources\FilametricsAccountResource::class,
            // Resources\FilametricsSettingResource::class,
        ]);


    }

    public function boot(Panel $panel): void
    {

    }
}
