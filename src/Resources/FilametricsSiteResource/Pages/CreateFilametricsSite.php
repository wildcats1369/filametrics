<?php

namespace wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;

class CreateFilametricsSite extends CreateRecord
{
    // protected static string $view = 'wildcats1369::pages.default';
    protected static string $resource = FilametricsSiteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // Set the user_id from the logged-in user
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
