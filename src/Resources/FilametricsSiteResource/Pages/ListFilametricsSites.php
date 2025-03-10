<?php
namespace wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;

use Filament\Resources\Pages\ListRecords;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;
use Filament\Actions;
class ListFilametricsSites extends ListRecords
{
    protected static string $resource = FilametricsSiteResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
