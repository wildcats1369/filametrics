<?php

namespace wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Page;

use Illuminate\Support\Facades\Config;

use Filament\Forms\Contracts\HasForms;
use wildcats1369\Filametrics\Models\FilametricsAccount;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Pages\Concerns;

class FilametricSiteAccountPage extends EditRecord
{
    protected static string $resource = FilametricsSiteResource::class;
    protected static string $view = 'filametrics::pages.accounts';

    public FilametricsAccount $account;
    public array $subformFields = [];



    public function getTitle(): string
    {
        return 'Manage Filametric Site Account';
    }



    // public function loadSubformFields(): void
    // {
    //     $provider = $this->account->provider ?? Config::get('filametrics.default_provider');
    //     $this->subformFields = Config::get("filametrics.subform.{$provider}", []);
    // }





    // public function form(Form $form): Form
    // {
    //     return $form->schema($this->getFormSchema());
    // }
}
