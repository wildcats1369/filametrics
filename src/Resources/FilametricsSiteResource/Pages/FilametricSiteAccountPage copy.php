<?php
namespace wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;

use Filament\Resources\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Config;
use wildcats1369\Filametrics\Models\FilametricsAccount;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;
use Illuminate\Contracts\View\View;
class FilametricSiteAccountPage extends Page
{
    protected static string $resource = FilametricsSiteResource::class;
    protected static string $view = 'filametrics::livewire.accounts';

    public FilametricsAccount $account;
    public array $subformFields = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', ['filametrics_site' => $this->record->filametrics_site_id]);
    }

    public function mount()
    {
        $this->account = new FilametricsAccount();
        $this->loadSubformFields();
    }

    public function loadSubformFields()
    {
        $provider = $this->account->provider ?? Config::get('filametrics.default_provider');
        $this->subformFields = Config::get("filametrics.subform.{$provider}", []);
    }

    public function updated($property)
    {
        if ($property === 'account.provider') {
            $this->loadSubformFields();
        }
    }

    public function getFormSchema(): array
    {
        dd('getFormSchema');
        return [
            TextInput::make('name')->required(),
            TextInput::make('label')->required(),
            Select::make('type')
                ->options([
                    'text' => 'Text',
                    'numeric' => 'Numeric',
                    'upload' => 'Upload',
                ])->required(),
            Select::make('provider')
                ->options(Config::get('filametrics.providers.select'))
                ->required()
                ->reactive(),
            Placeholder::make('subform')
                ->content(fn () => view('filametrics::livewire.subform', ['fields' => $this->subformFields])),
        ];
    }

    public function submit()
    {
        $this->validate();
        $this->account->save();

        foreach ($this->subformFields as $field => $type) {
            $this->account->$field = $this->$field;
        }

        $this->account->save();
        session()->flash('success', 'Account saved successfully.');
    }

    public function render(): View
    {
        return view('filametrics::pages.accounts');
    }


}
