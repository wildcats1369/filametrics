<?php

namespace wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use wildcats1369\Filametrics\Models\FilametricsSite;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use Filament\Infolists\Components\Split;

class PdfFilametricSite extends ViewRecord
{
    protected static string $resource = FilametricsSiteResource::class;

    protected static string $view = 'filametrics::pages.pdf-view';

    public \Illuminate\Database\Eloquent\Model|string|int|null $record;

    public $period;

    public function mount($record): void
    {
        $this->record = FilametricsSite::find($record);

    }
    public function getViewData(): array
    {
        $record = $this->record;

        $infolist = Infolist::make()
            ->record($record);

        $infolist = $this->getSchema($infolist);
        $period = $this->getPeriod();
        return [
            'record' => $record,
            'infolist' => $infolist,
            'start_date' => $period['start'],
            'end_date' => $period['end'],
        ];
    }



    public static function getSchema(Infolist $infolist): Infolist
    {
        $requestData = App::make('request');

        $firstDayOfPreviousMonth = $requestData['start_date'] ?? Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastDayOfPreviousMonth = $requestData['end_date'] ?? Carbon::now()->subMonth()->endOfMonth()->toDateString();
        $period = [
            'start' => $firstDayOfPreviousMonth,
            'end' => $lastDayOfPreviousMonth,
        ];

        return $infolist
            ->schema([
                self::getChannelGroupSchema($period, $infolist->record),
                self::getAudienceSchema($period, $infolist->record, "Overall User Acquisition"),
                self::getAudienceSchema($period, $infolist->record, "Chinese Market Acquisition", "country:china"),
                self::getLanguageSchema($period, $infolist->record, "Language breakdown (All User)"),
                self::getLanguageSchema($period, $infolist->record, "Language breakdown (Chinese Market)", "country:china"),
                self::getDeviceSchema($period, $infolist->record, "Device breakdown (All User)"),
                self::getDeviceSchema($period, $infolist->record, "Device breakdown (Chinese Market)", "country:china"),

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
                    ]),
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
                    'description' => '',
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
                ])->schema([
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

    public static function getMiddleware(): array
    {
        return []; // Remove all middleware (no auth, no panel auth either)
    }
    public static function canAccess(array $parameters = []): bool
    {
        return true;
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return false;
    }

    public function getPeriod(): array
    {
        $requestData = App::make('request');

        $firstDayOfPreviousMonth = $requestData['start_date'] ?? Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastDayOfPreviousMonth = $requestData['end_date'] ?? Carbon::now()->subMonth()->endOfMonth()->toDateString();
        $this->period = [
            'start' => $firstDayOfPreviousMonth,
            'end' => $lastDayOfPreviousMonth,
        ];
        return $this->period;
    }

}