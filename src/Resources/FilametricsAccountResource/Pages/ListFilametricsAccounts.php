<?php

namespace wildcats1369\Filametrics\Resources\FilametricsAccountResource\Pages;

use Filament\Resources\Pages\ListRecords;
use wildcats1369\Filametrics\Resources\FilametricsAccountResource;
use Filament\Actions;

class ListFilametricsAccounts extends ListRecords
{
    protected static string $resource = FilametricsAccountResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
