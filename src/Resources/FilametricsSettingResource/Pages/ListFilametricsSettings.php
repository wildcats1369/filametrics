<?php

namespace wildcats1369\Filametrics\Resources\FilametricsSettingResource\Pages;

use Filament\Resources\Pages\ListRecords;
use wildcats1369\Filametrics\Resources\FilametricsSettingResource;
use Filament\Actions;
class ListFilametricsSettings extends ListRecords
{
    protected static string $resource = FilametricsSettingResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
