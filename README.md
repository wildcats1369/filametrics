-- Export the policies
php artisan vendor:publish --tag=policies

https://www.conroyp.com/articles/avoiding-route-filament-admin-auth-login-not-defined

Needed Widgets
BarChartH done
BarChartV done
LineChart done
StatsChart
PieChart

WidgetData
- heading
- description
- record
- [start_date,end_date] Period
- metric
- dimension
- metric-filter
- dimension-filter


SESSION
array:2 [▼ // C:\Codes\Projects\docker-setup\src\laravel-packages\wildcats1369\filametrics\src\Helpers\Google\Widgets\SessionsDurationWidget.php:65
  "previous" => "17.587794"
  "result" => 0
]
return FilamentGoogleAnalytics::for($data['result'])
            ->previous($data['previous'])
            ->format('%');

+previous: 17.587794
+format: "%"
+value: 0

USER
array:1 [▼ // C:\Codes\Projects\docker-setup\src\laravel-packages\wildcats1369\filametrics\src\Helpers\Google\Widgets\ActiveUsersSevenDayWidget.php:68
  "results" => array:6 [▼
    "Apr 2" => 14
    "Apr 3" => 16
    "Apr 4" => 16
    "Apr 5" => 15
    "Apr 6" => 13
    "Apr 7" => 7
  ]
]

$data = Arr::get(
            $lookups,
            $this->filter,
            [
                'results' => [0],
            ],
        );