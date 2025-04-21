<?php
namespace wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;

use Filament\Pages\Page;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;
use Filament\Infolists\Infolist;
use wildcats1369\Filametrics\Models\FilametricsSite;
use Log;
ini_set('memory_limit', '1024M');
class PdfFilametricSite extends Page
{
    protected static string $resource = FilametricsSiteResource::class;
    public $model;
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static string $view = 'filametrics::pages.pdf-view';

    public function mount($record)
    {
        $this->model = FilametricsSite::find($record);
        $this->generatePdf($this->model);
    }

    public function generatePdf($record)
    {
        $html = $this->generateHtml($record); // Method to generate HTML content
        dd('generatePdf', $html);
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isRemoteEnabled' => true]);
        $pdf->showImageErrors = true;
        $pdf->render();

        return $pdf->stream('viewPage.pdf', ['attachment' => false]);
    }

    private function generateHtml($record)
    {
        $requestData = App::make('request');
        $firstDayOfPreviousMonth = $requestData['start_date'] ?? Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastDayOfPreviousMonth = $requestData['end_date'] ?? Carbon::now()->subMonth()->endOfMonth()->toDateString();
        $period = [
            'start' => $firstDayOfPreviousMonth,
            'end' => $lastDayOfPreviousMonth,
        ];

        $info = new Infolist();
        $info->record($record);

        $infolist = FilametricsSiteResource::infolist($info);
        // return $infolist->toHtml();
        return view('filametrics::pages.pdf-view', [
            'infolist' => $infolist->toHtml(),
            'period' => $period,
            'record' => $record,
        ]);
    }
}