<?php

namespace wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;
use wildcats1369\Filametrics\Models\FilametricsSite;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;

class PredictFilametricsSite extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static string $view = 'filametrics::pages.predict';
    protected static ?string $navigationLabel = 'External API Data';

    protected static string $resource = FilametricsSiteResource::class;
    protected static string $route = '/{record}/predict';

    public FilametricsSite $record;

    protected function getApiData(): array
    {
        $payload = [
            'property_id' => $this->record->view_id ?? '373507543',
        ];

        $response = Http::post(env('PREDICT_API_HOST', 'http://127.0.0.1:5000').'/predict', $payload);

        if (! $response->successful()) {
            return [
                'status' => 'error',
                'message' => 'API call failed',
            ];
        }

        $data = $response->json();
        $predictions = $data['prediction'] ?? [];

        // --- Threshold table config ---
        $thresholds = [
            'users' => [
                'switch' => 500,
                'percent' => [20, 40],
                'fixed' => [100, 300],
            ],
            'sessions' => [
                'switch' => 500,
                'percent' => [20, 40],
                'fixed' => [100, 300],
            ],
            'pageviews' => [
                'switch' => 1000,
                'percent' => [20, 40],
                'fixed' => [200, 500],
            ],
            'bounce_rate' => [
                'switch' => 30,
                'percent' => [10, 20],
                'fixed' => [3, 6],
                'direction' => 'increase',
            ],
            'avg_session_duration' => [
                'switch' => 60,
                'percent' => [15, 30],
                'fixed' => [10, 20],
                'direction' => 'decrease',
            ],
            'conversions' => [
                'switch' => 10,
                'percent' => [20, 40],
                'fixed' => [2, 4],
            ],
        ];

        // --- Evaluation Logic ---
        foreach ($predictions as $metric => &$entry) {
            if (isset($entry['error']) || ! isset($entry['prev']) || ! isset($entry['next'])) {
                $entry['status'] = 'UNKNOWN';
                continue;
            }

            $prev = floatval($entry['prev']);
            $next = floatval($entry['next']);
            $diff = $next - $prev;
            $absDiff = abs($diff);

            $rule = $thresholds[$metric] ?? null;

            if (! $rule || $prev == 0) {
                $entry['status'] = 'OK';
                continue;
            }

            // Directional logic (for bounce rate / session duration)
            $direction = $rule['direction'] ?? 'decrease'; // default for downward metrics
            $isBad = fn ($d) => $direction === 'increase' ? $d > 0 : $d < 0;

            // Pick logic: fixed vs percent
            if ($prev < $rule['switch']) {
                // Use fixed
                [$warn, $crit] = $rule['fixed'];
                if ($isBad($diff) && $absDiff >= $crit) {
                    $entry['status'] = 'CRITICAL';
                } elseif ($isBad($diff) && $absDiff >= $warn) {
                    $entry['status'] = 'WARNING';
                } else {
                    $entry['status'] = 'OK';
                }
            } else {
                // Use percentage
                [$warnPct, $critPct] = $rule['percent'];
                $pctDiff = abs($diff / $prev) * 100;

                if ($isBad($diff) && $pctDiff >= $critPct) {
                    $entry['status'] = 'CRITICAL';
                } elseif ($isBad($diff) && $pctDiff >= $warnPct) {
                    $entry['status'] = 'WARNING';
                } else {
                    $entry['status'] = 'OK';
                }
            }
        }

        $data['prediction'] = $predictions;
        return $data;
    }

    public function getViewData(): array
    {
        return [
            'data' => $this->getApiData(),
            'domain' => $this->record->domain_name ?? $this->record->domain_name ?? 'N/A',
        ];
    }
}
