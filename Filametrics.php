<?php

namespace wildcats1369\Filametrics;

use wildcats1369\Filametrics\Pages\SettingsPage;
use wildcats1369\Filametrics\Resources;
use Filament\Contracts\Plugin;
use Filament\Panel;

class FilametricsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filametrics';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                Resources\FilametricsSiteResource::class,
                Resources\FilametricsAccountResource::class,
            ])
            ->pages([
                // SettingsPage::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Optional: Add any additional logic you want on boot
    }
}
