<?php
namespace wildcats1369\Filametrics\Helpers\Google\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;


class DateRangeFilter extends Widget
{
    protected static string $view = 'filametrics::widgets.google.view.date-range-filter';

    public $startDate;
    public $endDate;

    public function mount(Request $request)
    {
        // $this->startDate = $this->startDate ?? $request->input('start_date', Carbon::now()->subMonth()->startOfMonth()->toDateString());
        // $this->endDate = $this->endDate ?? $request->input('end_date', Carbon::now()->subMonth()->endOfMonth()->toDateString());
    }

    public function submit()
    {
        // return redirect()->to(url()->current().'?start_date='.$this->startDate.'&end_date='.$this->endDate);
    }


    // protected function getFormSchema(): array
    // {
    //     return [
    //         DatePicker::make('start_date')
    //             ->label('Start Date')
    //             ->default($this->startDate)
    //             ->required(),
    //         DatePicker::make('end_date')
    //             ->label('End Date')
    //             ->default($this->endDate)
    //             ->required(),
    //     ];
    // }

}