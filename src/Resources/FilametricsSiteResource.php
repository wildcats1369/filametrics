<?php

namespace wildcats1369\Filametrics\Resources;

use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use wildcats1369\Filametrics\Models\FilametricsSite;
use wildcats1369\Filametrics\Models\FilametricsAccount;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource\RelationManagers\FilametricsAccountRelationManager;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Forms\Components\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use wildcats1369\Filametrics\Helpers\Google\Widgets;


class FilametricsSiteResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = FilametricsSite::class;

    protected static ?string $navigationLabel = 'Sites';
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Filametrics';

    protected static ?int $navigationSort = 1;

    public $accounts;
    public $existing_accounts;
    public $account_forms;


    // public function beforeFill($record): void
    // {
    //     $existing_accounts = FilametricsAccount::where('site_id', $record->id)->get()->toArray();
    //     \Log::info('beforeFill: ', $existing_accounts);
    //     $this->existing_accounts = $existing_accounts;
    // }

    /*************  ✨ Codeium Command ⭐  *************/
    /**
     * Returns the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    /******  81c08c5d-cee1-4fd6-b288-a41780e52470  *******/
    public static function form(Forms\Form $form): Forms\Form
    {

        $schema = [
            Forms\Components\TextInput::make('domain_name')->required(),
            Forms\Components\TextInput::make('view_id')->label('View ID'),
            // Forms\Components\Toggle::make('is_visible_to_all')->label('Visible to All')->default(false),
            Forms\Components\Repeater::make('account_forms')
                // ->relationship('account_forms')
                ->schema(function (callable $get, callable $set) {
                    $included = $get('providers') ?? [];
                    $schema = [
                        Forms\Components\Select::make('provider')
                            ->options([
                                'google' => 'Google',
                                'moz' => 'Moz',
                            ])
                            ->label('Provider')
                            ->default(fn ($record) => $record->provider)
                            ->reactive()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        // ->disableOptionWhen(fn (string $value): bool => in_array($value, $included))
                        ,
                        Forms\Components\TextInput::make('property_id')
                            ->label('Property ID')
                            ->required()
                            ->visible(function ($record, $get) {

                                return $get('provider') === 'google';
                            }),
                        Forms\Components\FileUpload::make('service_account_credentials_json')
                            ->label('Service Account Credentials JSON')
                            ->required()
                            ->disk('local')
                            ->directory('analytics')
                            ->visible(function ($record, $get) {
                                return $get('provider') === 'google';
                            }),
                        $schema[] = Forms\Components\TextInput::make('api_id')
                            ->label('API ID')
                            ->required()
                            ->visible(function ($record, $get) {
                                return $get('provider') === 'moz';
                            }),

                    ];


                    return $schema;
                })->itemLabel(fn (array $state): ?string => $state['provider'] ?? null)
                ->label('Add Accounts')
                ->deleteAction(
                    fn (Action $action) => $action->requiresConfirmation(),
                )

            ,

        ];


        return $form->schema($schema);
    }

    public function updatedSelectedProvider($value)
    {
        $this->selectedProvider = $value;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('domain_name')->label('Domain Name'),
            ]);
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

    // public static function getRelations(): array
    // {
    //     return [
    //         FilametricsAccountRelationManager::class,
    //     ];
    // }

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
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\PageViewsWidget::class,
            Widgets\VisitorsWidget::class,
            Widgets\ActiveUsersOneDayWidget::class,
            Widgets\ActiveUsersSevenDayWidget::class,
            Widgets\ActiveUsersTwentyEightDayWidget::class,
            Widgets\SessionsWidget::class,
            Widgets\SessionsDurationWidget::class,
            Widgets\SessionsByCountryWidget::class,
            Widgets\SessionsByDeviceWidget::class,
            Widgets\MostVisitedPagesWidget::class,
            Widgets\TopReferrersListWidget::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\PageViewsWidget::class,
            Widgets\VisitorsWidget::class,
            Widgets\ActiveUsersOneDayWidget::class,
            Widgets\ActiveUsersSevenDayWidget::class,
            Widgets\ActiveUsersTwentyEightDayWidget::class,
            Widgets\SessionsWidget::class,
            Widgets\SessionsDurationWidget::class,
            Widgets\SessionsByCountryWidget::class,
            Widgets\SessionsByDeviceWidget::class,
            Widgets\MostVisitedPagesWidget::class,
            Widgets\TopReferrersListWidget::class,
        ];
    }
}