<?php

namespace wildcats1369\Filametrics\Providers;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Filament\Panel;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;
use wildcats1369\Filametrics\Resources\FilametricsAccountResource;
use wildcats1369\Filametrics\Resources\FilametricsSettingResource;
use Filament\Facades\Filament;
use wildcats1369\Filametrics\FilametricsPlugin;
use wildcats1369\Filametrics\Policies;
use wildcats1369\Filametrics\Models;
use Illuminate\Support\Facades\Gate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin; // Import the Shield plugin
use Livewire\Livewire;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


class FilametricsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filametrics')
            ->hasConfigFile()
            ->hasMigrations()
            ->hasRoute('web');
    }

    public function getId(): string
    {
        return 'filametrics';
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('filametrics')
            ->resources([
                FilametricsSiteResource::class,
                FilametricsAccountResource::class,
                // FilametricsSettingResource::class,
            ])
            ->plugins([
                new FilametricsPlugin(),
                FilamentShieldPlugin::make(), // Attach the Shield plugin here
            ]);
    }

    public function boot(): void
    {
        parent::boot();

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'filametrics');

        Livewire::component('filametrics-account-form', \wildcats1369\Filametrics\Http\Livewire\FilametricsAccountForm::class);


        // Publish the Policies directory
        $this->publishes([
            __DIR__.'/../Policies' => base_path('app/Policies'),
        ], 'policies');

        // Register policies manually
        Gate::policy(Models\FilametricsAccount::class, Policies\FilametricsAccountPolicy::class);
        Gate::policy(Models\FilametricsSetting::class, Policies\FilametricsSettingPolicy::class);
        Gate::policy(Models\FilametricsSite::class, Policies\FilametricsSitePolicy::class);
        Filament::registerResources([
            FilametricsSiteResource::class,
            FilametricsAccountResource::class,
            // FilametricsSettingResource::class,
        ]);


        Panel::make()
            ->id('filametrics')
            ->resources([
                FilametricsSiteResource::class,
                FilametricsAccountResource::class,
                // FilametricsSettingResource::class,
            ])
            ->plugins([
                new FilametricsPlugin(),
                FilamentShieldPlugin::make(), // Attach the Shield plugin here
            ])
            ->register();

        $this->loadMigrationsFrom(__DIR__.'/../migrations');

        self::registerWidgets('wildcats1369\Filametrics\Helpers\Google\Widgets', __DIR__.'/../Helpers/Google/Widgets', 'filametrics-google-widgets-');
    }

    public static function registerWidgets($namespace, $directory, $prefix)
    {
        $files = File::allFiles($directory);

        foreach ($files as $file) {
            $class = $namespace.'\\'.$file->getFilenameWithoutExtension();
            $classname = $prefix.$file->getFilenameWithoutExtension();
            if (class_exists($class)) {
                $componentName = Str::kebab($classname);
                Livewire::component($componentName, $class);
            }
        }
    }

}
