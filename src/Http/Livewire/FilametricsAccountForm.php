<?php
namespace wildcats1369\Filametrics\Http\Livewire;

use Livewire\Component;
use wildcats1369\Filametrics\Models\FilametricsAccount;
use Illuminate\Support\Facades\Config;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages\FilametricSiteAccountPage;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Concerns;
use Filament\Pages\Concerns\InteractsWithFormActions;




class FilametricsAccountForm extends Component implements HasForms
{
    use InteractsWithForms;
    // use Concerns\HasRelationManagers;
    // use Concerns\InteractsWithRecord {
    //     configureAction as configureActionRecord;
    // }
    use InteractsWithFormActions;
    public FilametricsAccount $account;

    protected static string $resource = FilametricsSiteResource::class;

    public ?array $data = [];
    public array $formSchema = [];
    public array $subformFields = [];

    // public $record;

    public function mount(FilametricSiteAccountPage $page)
    {
        // parent::mount($page);
        // dd('mount', $page);
        // $this->form->fill();
        // $this->record = $page->record;

        // $this->account = new FilametricsAccount();
        // $this->loadSubformFields();
        // $this->initializeFormSchema();
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

    public function form(Form $form): Form
    {

        return $form->schema([
            Forms\Components\TextInput::make('domain_name')->required(),
            Forms\Components\TextInput::make('view_id')->label('View ID'),
            // Forms\Components\Toggle::make('is_visible_to_all')->label('Visible to All')->default(false),
        ]);

        // return $form
        //     ->schema([
        //         TextInput::make('name')->required(),
        //         TextInput::make('label')->required(),
        //         Select::make('type')
        //             ->options([
        //                 'text' => 'Text',
        //                 'numeric' => 'Numeric',
        //                 'upload' => 'Upload',
        //             ])->required(),
        //         Select::make('provider')
        //             ->options(Config::get('filametrics.providers.select'))
        //             ->required()
        //             ->reactive(),
        //         Toggle::make('is_active')->label('Active')->default(true),
        //         Placeholder::make('subform')
        //             ->content(fn () => view('filametrics::livewire.subform', ['fields' => $this->subformFields])),
        //     ]);
    }

    // public static function table(Tables\Table $table): Tables\Table
    // {
    //     return $table->columns([
    //         Tables\Columns\TextColumn::make('domain_name')->sortable()->searchable(),
    //         Tables\Columns\TextColumn::make('view_id'),
    //         Tables\Columns\BooleanColumn::make('is_visible_to_all')->label('Visible'),
    //         Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
    //         Tables\Columns\TextColumn::make('updated_at')->label('Last Updated')->dateTime(),
    //     ])
    //         ->actions([
    //             Tables\Actions\ViewAction::make(),
    //             Tables\Actions\EditAction::make(),
    //             Tables\Actions\Action::make('accounts')
    //                 ->label('Accounts')
    //                 ->url(fn ($record) => url("filametrics-sites/{$record->id}/accounts"))
    //                 ->icon('heroicon-o-link')
    //                 ->requiresConfirmation()
    //                 ->color('primary')
    //                 ->button(),
    //         ]);
    // }

    public function render()
    {
        // dd($this->record);
        return view('filametrics::livewire.account-form', [
            'formSchema' => $this->formSchema,
        ]);
    }


}
