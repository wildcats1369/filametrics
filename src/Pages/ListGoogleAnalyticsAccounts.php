<?php

namespace wildcats1369\Filametrics\Pages;

use wildcats1369\Filametrics\Resources\Filametrics;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGoogleAnalyticsAccounts extends ListRecords
{
    protected static string $resource = Filametrics::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
