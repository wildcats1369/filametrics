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



class ViewFilametricsSite extends ViewRecord
{
    protected static string $resource = FilametricsSiteResource::class;
    public $google_client;

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
            Widgets\TopReferrersListWidget::class,
        ];

    }



    protected function getHeaderActions(): array
    {
        $requestData = App::make('request');

        $firstDayOfPreviousMonth = $requestData['start_date'] ?? Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastDayOfPreviousMonth = $requestData['end_date'] ?? Carbon::now()->subMonth()->endOfMonth()->toDateString();
        $this->period = [
            'start' => $firstDayOfPreviousMonth,
            'end' => $lastDayOfPreviousMonth,
        ];
        return [
            Action::make('Edit')
                ->label('Edit')
                ->action(function () {
                    return redirect()->route('filament.admin.resources.filametrics-sites.edit', ['record' => $this->record->id]);
                })
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->button(),
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
