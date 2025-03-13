<?php

namespace wildcats1369\Filametrics\Resources;

use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use wildcats1369\Filametrics\Models\FilametricsSite;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource\RelationManagers\FilametricsAccountRelationManager;
use Filament\Resources\RelationManagers\RelationGroup;
use Log;

class FilametricsSiteResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = FilametricsSite::class;

    protected static ?string $navigationLabel = 'Sites';
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Filametrics';

    protected static ?int $navigationSort = 1;

    public $accounts;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('domain_name')->required(),
            Forms\Components\TextInput::make('view_id')->label('View ID'),
            Forms\Components\Toggle::make('is_visible_to_all')->label('Visible to All')->default(false),
            Forms\Components\Repeater::make('accounts')
                ->relationship('accounts')
                ->schema(function (callable $get, callable $set) {
                    $schema = [
                        Forms\Components\Select::make('provider')
                            ->options([
                                'google' => 'Google',
                                'moz' => 'Moz',
                            ])
                            ->label('Provider')
                            ->reactive()
                            ->afterStateUpdated(function ($state) use ($set) {
                                $set('selectedProvider', $state);
                            }),
                    ];

                    if ($get('selectedProvider') === 'google') {
                        $schema[] = Forms\Components\TextInput::make('property_id')
                            ->label('Property ID')
                            ->required();
                        $schema[] = Forms\Components\FileUpload::make('service_account_credentials_json')
                            ->label('Service Account Credentials JSON')
                            ->required();
                    } elseif ($get('selectedProvider') === 'moz') {
                        $schema[] = Forms\Components\TextInput::make('api_id')
                            ->label('API ID')
                            ->required();
                    }

                    return $schema;
                })
                ->label('Accounts')
                ->collapsed(false),
            // Forms\Components\HasManyRepeater::make('accounts')
            //     ->relationship('accounts')
            //     ->schema([
            //         Forms\Components\TextInput::make('name')->required(),
            //         Forms\Components\TextInput::make('label')->required(),
            //         Forms\Components\TextInput::make('type')->required(),
            //         Forms\Components\TextInput::make('provider')->nullable(),
            //     ])
            //     ->label('Add Account'),
            // Forms\Components\BelongsToManyMultiSelect::make('accounts')
            //     ->relationship('filametrics_accounts', 'filametrics_sites'),
        ]);
    }

    protected function beforeSave(array $data): array
    {
        Log::info('mutateFormDataBeforeSave');
        $transformedAccounts = [];

        foreach ($data['accounts'] as $account) {
            if ($account['provider'] === 'google') {
                $transformedAccounts[] = [
                    'name' => 'property_id',
                    'label' => 'Property ID',
                    'type' => 'text_input',
                    'value' => $account['property_id'],
                    'provider' => $account['provider'],
                    'site_id' => $this->record->id,
                ];
                $transformedAccounts[] = [
                    'name' => 'service_account_credentials_json',
                    'label' => 'Service Account Credentials JSON',
                    'type' => 'file_upload',
                    'value' => $account['service_account_credentials_json'],
                    'provider' => $account['provider'],
                    'site_id' => $this->record->id,
                ];
            } elseif ($account['provider'] === 'moz') {
                $transformedAccounts[] = [
                    'name' => 'api_id',
                    'label' => 'API ID',
                    'type' => 'text_input',
                    'value' => $account['api_id'],
                    'provider' => $account['provider'],
                    'site_id' => $this->record->id,
                ];
            }
        }

        $data['accounts'] = $transformedAccounts;

        return $data;
    }

    public function save()
    {
        $data = $this->form->getState();

        // Transform accounts data before saving
        $transformedData = $this->mutateFormDataBeforeSave($data);

        // Clear existing accounts and save transformed data
        $this->record->accounts()->delete();
        $this->record->accounts()->createMany($transformedData['accounts']);

        parent::save(); // Call the parent save method
    }


    public function updatedSelectedProvider($value)
    {
        $this->selectedProvider = $value;
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('domain_name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('view_id'),
            Tables\Columns\BooleanColumn::make('is_visible_to_all')->label('Visible'),
            Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
            Tables\Columns\TextColumn::make('updated_at')->label('Last Updated')->dateTime(),
        ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('createAccount')
                    ->label('Create Account')
                    ->action(function ($record) {
                        return redirect()->route('filament.admin.resources.filametrics-accounts.create', ['record' => $record->id]);
                    })
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->button(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFilametricsSites::route('/'),
            'create' => Pages\CreateFilametricsSite::route('/create'),
            'edit' => Pages\EditFilametricsSite::route('/{record}/edit'),
            'view' => Pages\ViewFilametricsSite::route('/{record}'),
            // 'accounts' => Pages\FilametricSiteAccountPage::route('/{record}/accounts'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            FilametricsAccountRelationManager::class,
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            // 'filametricsSite:create',
            // 'filametricsSite:update',
            // 'filametricsSite:delete',
            // 'filametricsSite:pagination',
            // 'filametricsSite:detail',
        ];
    }
}



