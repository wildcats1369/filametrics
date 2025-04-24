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
use Filament\Infolists\Components\ViewEntry;
use wildcats1369\Filametrics\Helpers\Google\Widgets;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\View;
use Filament\Infolists\Components\CustomEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Carbon\Carbon;
use Spatie\Analytics\Period;
use Log;
use Filament\Infolists\Components\Split;
use Filament\Forms\Components\DatePicker;
use Storage;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Str;





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
    public static $period;

    public static $requestData;

    public static function infolist(Infolist $infolist): Infolist
    {
        Log::info('infolist: '.json_encode($infolist));
        // if ($infolist->record == null)
        //     return $infolist->schema([]);
        $requestData = App::make('request');

        $firstDayOfPreviousMonth = $requestData['start_date'] ?? Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastDayOfPreviousMonth = $requestData['end_date'] ?? Carbon::now()->subMonth()->endOfMonth()->toDateString();
        $period = [
            'start' => $firstDayOfPreviousMonth,
            'end' => $lastDayOfPreviousMonth,
        ];

        return $infolist
            ->schema([

                TextEntry::make('domain_name')->label('Domain Name'),
                ViewEntry::make('status')->view('filametrics::widgets.google.date-range-filter', [
                    'startDate' => $period['start'],
                    'endDate' => $period['end'],
                ]),
                self::getChannelGroupSchema($period, $infolist->record),
                Split::make([
                    self::getAudienceSchema($period, $infolist->record, "Overall User Acquisition"),
                    self::getAudienceSchema($period, $infolist->record, "Chinese Market Acquisition", "country:china"),
                ])->from('md')->columnSpan(2),
                Split::make([
                    self::getLanguageSchema($period, $infolist->record, "Language breakdown (All User)"),
                    self::getLanguageSchema($period, $infolist->record, "Language breakdown (Chinese Market)", "country:china"),

                ])->from('md')->columnSpan(2),
                Split::make([
                    self::getDeviceSchema($period, $infolist->record, "Device breakdown (All User)"),
                    self::getDeviceSchema($period, $infolist->record, "Device breakdown (Chinese Market)", "country:china"),
                ])->from('md')->columnSpan(2),
                Section::make('Top Pages')
                    ->label('Top Pages')
                    ->schema([
                        ViewEntry::make('status')->view('filametrics::widgets.google.top-list', [
                            'record' => $infolist->record,
                            'heading' => '',
                            'description' => '',
                            'period' => $period,
                            'metric' => 'activeUsers',
                            'dimensions' => 'pageReferrer',
                            'metric_filter' => null,
                            'dimension_filter' => null,
                        ]),
                    ])
                ,
            ]);
    }

    public static function getDeviceSchema($period, $record, $label = "", $dimension_filter = null)
    {
        return Section::make($label)
            ->label($label)
            ->schema([
                ViewEntry::make('status')->view('filametrics::widgets.google.pie', [
                    'record' => $record,
                    'heading' => '',
                    'description' => '',    // optional
                    'period' => $period,
                    'metric' => 'sessions',
                    'dimensions' => 'deviceCategory',
                    'metric_filter' => null,
                    'dimension_filter' => $dimension_filter,
                ]),
                ViewEntry::make('status')->view('filametrics::widgets.google.top-list', [
                    'record' => $record,
                    'heading' => '',
                    'description' => '',
                    'period' => $period,
                    'metric' => 'sessions',
                    'dimensions' => 'deviceCategory',
                    'metric_filter' => null,
                    'dimension_filter' => $dimension_filter,
                ]),
            ]);
    }

    public static function getChannelGroupSchema($period, $record)
    {

        return Section::make('Analytics')
            ->label('Google')
            ->schema([
                ViewEntry::make('status')->view('filametrics::widgets.google.bar-chart-h', [
                    'record' => $record,
                    'heading' => 'WHERE IS TRAFFIC COMING FROM?',
                    'description' => '',
                    'period' => $period,
                    'metric' => 'sessions',
                    'dimensions' => 'sessionDefaultChannelGroup',
                    'metric_filter' => null,
                    'dimension_filter' => null,
                ]),


            ]);
    }

    public static function getAudienceSchema($period, $record, $label = "", $dimension_filter = null)
    {

        return Section::make($label)
            ->label($label)
            ->schema([
                ViewEntry::make('status')->view('filametrics::widgets.google.line-chart', [
                    'record' => $record,
                    'heading' => 'Your audience at a glance',
                    'description' => '',
                    'period' => $period,
                    'metric' => 'activeUsers',
                    'dimensions' => 'date',
                    'metric_filter' => null,
                    'dimension_filter' => $dimension_filter,
                ]),
                Grid::make([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 3,
                    'xl' => 3,
                    '2xl' => 3,
                ])
                    ->schema([
                        ViewEntry::make('status')->view('filametrics::widgets.google.stats', [
                            'record' => $record,
                            'heading' => 'Users',
                            'description' => '',
                            'period' => $period,
                            'metric' => 'activeUsers',
                            'dimensions' => 'date',
                            'metric_filter' => null,
                            'dimension_filter' => $dimension_filter,
                        ]),
                        ViewEntry::make('status')->view('filametrics::widgets.google.stats', [
                            'record' => $record,
                            'heading' => 'Sessions Per User',
                            'description' => '',
                            'period' => $period,
                            'metric' => 'sessionsPerUser',
                            'dimensions' => 'date',
                            'metric_filter' => null,
                            'dimension_filter' => $dimension_filter,
                        ]),
                        ViewEntry::make('status')->view('filametrics::widgets.google.stats', [
                            'record' => $record,
                            'heading' => 'Views',
                            'description' => '',
                            'period' => $period,
                            'metric' => 'screenPageViews',
                            'dimensions' => 'date',
                            'metric_filter' => null,
                            'dimension_filter' => $dimension_filter,
                        ]),
                        ViewEntry::make('status')->view('filametrics::widgets.google.stats', [
                            'record' => $record,
                            'heading' => 'Bounce Rate',
                            'description' => '',
                            'period' => $period,
                            'metric' => 'bounceRate',
                            'dimensions' => 'date',
                            'metric_filter' => null,
                            'dimension_filter' => $dimension_filter,
                        ]),
                    ]),
            ]);

    }

    public static function getLanguageSchema($period, $record, $label = "", $dimension_filter = null)
    {

        return Section::make($label)
            ->label($label)
            ->schema([
                ViewEntry::make('status')->view('filametrics::widgets.google.bar-chart-v', [
                    'record' => $record,
                    'heading' => '',
                    'description' => '',
                    'period' => $period,
                    'metric' => 'activeUsers',
                    'dimensions' => 'language',
                    'metric_filter' => null,
                    'dimension_filter' => $dimension_filter,
                ]),
                ViewEntry::make('status')->view('filametrics::widgets.google.top-list', [
                    'record' => $record,
                    'heading' => '',
                    'description' => '',
                    'period' => $period,
                    'metric' => 'activeUsers',
                    'dimensions' => 'language',
                    'metric_filter' => null,
                    'dimension_filter' => $dimension_filter,
                ]),
            ]);

    }

    public function mount(Request $request)
    {
        Log::info('Mount method called');
        self::$requestData = $request->all();


    }

    /**
     * Returns the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
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
                            ->default(fn ($record) => $record->provider ?? null)
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
                ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)

            ,

        ];


        return $form->schema($schema);

    }

    public function updatedSelectedProvider($value)
    {
        $this->selectedProvider = $value;
    }



    public static function table(Tables\Table $table): Tables\Table
    {

        $firstDayOfPreviousMonth = date('Y-m-01', strtotime('first day of last month'));
        $lastDayOfPreviousMonth = date('Y-m-t', strtotime('last day of last month'));

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

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFilametricsSites::route('/'),
            'create' => Pages\CreateFilametricsSite::route('/create'),
            'edit' => Pages\EditFilametricsSite::route('/{record}/edit'),
            'view' => Pages\ViewFilametricsSite::route('/{record}'),
            // 'pdf' => Pages\PdfFilametricSite::route('/{record}/pdf'),
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
            Widgets\ChannelGroupWidget::class,
        ];
    }


    public static function downloadPDF($record)
    {
        $requestData = App::make('request');

        $firstDayOfPreviousMonth = $requestData['start_date'] ?? Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastDayOfPreviousMonth = $requestData['end_date'] ?? Carbon::now()->subMonth()->endOfMonth()->toDateString();
        $period = [
            'start' => $firstDayOfPreviousMonth,
            'end' => $lastDayOfPreviousMonth,
        ];

        // Render the infolist view
        $infolistView = view('filament.resources.filametrics-site-resource.infolist', [
            'record' => $record,
            'period' => $period,
        ])->render();

        // Generate the PDF
        $pdf = PDF::loadHTML($infolistView);

        return $pdf->download('widgets.pdf');
    }


}