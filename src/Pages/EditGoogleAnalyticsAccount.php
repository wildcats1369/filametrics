<?php

namespace wildcats1369\Filametrics\Pages;

use wildcats1369\Filametrics\Resources\Filametrics;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGoogleAnalyticsAccount extends EditRecord
{
    protected static string $resource = Filametrics::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
