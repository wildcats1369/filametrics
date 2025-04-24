<?php

namespace wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use wildcats1369\Filametrics\Models\FilametricsSite;
use wildcats1369\Filametrics\Helpers\Google\Widgets;
use Filament\Forms;
use Filament\Pages\Actions\Action;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use Filament\Infolists\Infolist;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Arr;
use Log;




class ViewFilametricsSite extends ViewRecord
{
    protected static string $resource = FilametricsSiteResource::class;
    public $google_client, $period;

    protected function getFooterWidgets(): array
    {
        return [
            // Widgets\ChannelGroupWidget::class,
            // Widgets\PageViewsWidget::class,
            // Widgets\VisitorsWidget::class,
            // Widgets\ActiveUsersOneDayWidget::class,
            // Widgets\ActiveUsersSevenDayWidget::class,
            // Widgets\ActiveUsersTwentyEightDayWidget::class,
            // Widgets\SessionsWidget::class,
            // Widgets\SessionsDurationWidget::class,
            // Widgets\SessionsByCountryWidget::class,
            // Widgets\SessionsByDeviceWidget::class,
            // Widgets\MostVisitedPagesWidget::class,
            // Widgets\TopReferrersListWidget::class,
        ];

    }


    public function mount($record): void
    {
        // dd($record);
        $this->record = FilametricsSite::find($record);
        $requestData = App::make('request');
        Log::info('requestData: '.json_encode($requestData));
        $firstDayOfPreviousMonth = $requestData['start_date'] ?? Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastDayOfPreviousMonth = $requestData['end_date'] ?? Carbon::now()->subMonth()->endOfMonth()->toDateString();
        $this->period = [
            'start' => $firstDayOfPreviousMonth,
            'end' => $lastDayOfPreviousMonth,
        ];

    }

    protected function getHeaderActions(): array
    {
        Log::info('outside: '.json_encode($this->period));

        return [
            Action::make('Edit')
                ->label('Edit')
                ->action(function () {
                    return redirect()->route('filament.admin.resources.filametrics-sites.edit', ['record' => $this->record->id]);
                })
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->button(),
            Action::make('download-pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    $record = $this->record;

                    $queryParams = array_filter([
                        'start_date' => $this->period['start'],
                        'end_date' => $this->period['end'],
                    ]);

                    $baseUrl = route('filament.admin.resources.filametrics-sites.pdf', ['record' => $record->id]);
                    $url = $baseUrl.'?'.Arr::query($queryParams);
                    $startFormatted = Carbon::parse($this->period['start'])->translatedFormat('F Y');
                    $domain = parse_url($record->domain_name, PHP_URL_HOST);
                    $filename = 'site-'.Str::slug($domain).'-'.$startFormatted.'.pdf';
                    $path = storage_path("app/public/{$filename}");

                    $sessionName = config('session.cookie');
                    $sessionValue = session()->getId();
                    $cookieDomain = parse_url(config('app.url'), PHP_URL_HOST);

                    Browsershot::url($url)
                        ->waitUntilNetworkIdle()
                        ->setOption('args', ['--no-sandbox'])
                        ->setCookies([
                            [
                                'name' => $sessionName,
                                'value' => $sessionValue,
                                'domain' => $cookieDomain,
                            ],
                        ])
                        ->format('A4')
                        ->timeout(60)
                        ->savePdf($path);

                    return response()->download($path)->deleteFileAfterSend();
                })

            ,

        ];
    }



    public function getDescription(): ?string
    {
        return 'Your description here';
    }


    protected function getFormSchema(): array
    {
        \Log::info('getFormSchema called');
        return [
            DateRangePicker::make('created_at')
                ->label('Created At')
                ->timezone('UTC') // Optional: Set timezone
                ->startDate(now()->subMonth()) // Optional: Set start date
                ->endDate(now()), // Optional: Set end date
        ];
    }


    protected function getContent(): array
    {
        return [
            Form::make()
                ->schema($this->getFormSchema())
                ->columns(1),
        ];
    }



    protected function getActions(): array
    {
        $requestData = App::make('request');

        $firstDayOfPreviousMonth = $requestData['start_date'] ?? Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastDayOfPreviousMonth = $requestData['end_date'] ?? Carbon::now()->subMonth()->endOfMonth()->toDateString();
        $this->period = [
            'start' => $firstDayOfPreviousMonth,
            'end' => $lastDayOfPreviousMonth,
        ];
        return [
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->action(function () {
                    $record = $this->record;
                    $period = $this->period;
                    $schema = $this->getInfolistSchema($record, $period);
                    $pdf = Pdf::loadView('filametrics::pdf.infolist', compact('record', 'period', 'schema'));
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, 'infolist.pdf');
                }),
        ];
    }

    protected function getInfolistSchema($record, $period)
    {
        return FilametricsSiteResource::infolist(new Infolist())->record($record)->schema();
    }





}
