<?php

namespace wildcats1369\Filametrics\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use wildcats1369\Filametrics\Helpers\JwtTokenService;
use wildcats1369\Filametrics\Models\FilametricsSite;
use wildcats1369\Filametrics\Models\FilametricsAccount;

class FilametricsSync extends Command
{
    protected $signature = 'filametrics:sync';

    protected $description = 'Sync Filametrics sites with prediction API (upload credentials, pull data, train model)';

    public function handle()
    {
        $apiHost = config('app.predict_api_host', env('PREDICT_API_HOST', 'http://127.0.0.1:5000'));
        $sites = FilametricsSite::all();

        if ($sites->isEmpty()) {
            $this->info('No Filametrics sites found.');
            return 0;
        }

        foreach ($sites as $site) {
            // Load Google-specific account for this site
            $account = FilametricsAccount::query()
                ->where('provider', 'google')
                ->where('site_id', $site->id)
                ->pluck('value', 'name');

            $propertyId = $account['property_id'] ?? null;
            $jsonPath = $account['service_account_credentials_json'] ?? null;

            if (! $propertyId || ! $jsonPath) {
                $this->warn("Skipping site: {$site->domain_name} — missing Google credentials or property_id.");
                continue;
            }

            // Inject into site object
            $site->property_id = (string) $propertyId;
            $site->json_path = $jsonPath;

            $this->info("Processing site: {$site->domain_name} (Property ID: {$site->property_id})");

            if (! $this->isSiteSetupInAPI($apiHost, $site)) {
                $this->warn("Site not set in prediction API. Uploading credentials...");

                if (! $this->uploadCredentials($apiHost, $site)) {
                    $this->error("Failed to upload credentials for site {$site->domain_name}");
                    continue;
                }

                $this->info("Credentials uploaded. Retrying data ingestion...");

                if (! $this->callPullAPI($apiHost, $site)) {
                    $this->error("Retry of data ingestion API failed for site {$site->domain_name}");
                    continue;
                }

                $this->info("Data ingestion succeeded after retry.");
            } else {
                $this->info("Site already set up in prediction API.");

                $this->info("Calling data ingestion API...");
                if (! $this->callPullAPI($apiHost, $site)) {
                    $this->error("Data ingestion API failed for site {$site->domain_name}");
                    continue;
                }

                $this->info("Data ingestion succeeded.");
            }

            $this->info("Calling train API...");
            if (! $this->callTrainAPI($apiHost, $site)) {
                $this->error("Train API failed for site {$site->domain_name}");
                continue;
            }

            $this->info("Training succeeded.");
        }

        $this->info('Filametrics sync completed.');
        return 0;
    }

    protected function authHeaders(array $payload): array
    {

        $jwtService = new JwtTokenService();
        $token = $jwtService->generateToken($payload);

        return [
            'Authorization' => 'Bearer '.$token,
        ];
    }

    protected function isSiteSetupInAPI(string $apiHost, $site): bool
    {
        $date = now()->format('Y-m-d');
        $payload = [
            'property_id' => (string) $site->property_id,
            'date' => $date,
            'timestamp' => time(),
        ];

        $jwtService = new JwtTokenService();
        $token = $jwtService->generateToken($payload);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post("$apiHost/predict", $payload);

        return $response->successful() && $response->json('status') === 'success';
    }

    protected function uploadCredentials(string $apiHost, $site): bool
    {
        var_dump($site);
        $jsonPath = storage_path('app/private/analytics'.($site->service_account_file ?? ''));
        var_dump($jsonPath);

        if (! file_exists($jsonPath)) {
            $this->error("Credentials file not found at: $jsonPath");
            return false;
        }

        $payload = [
            'property_id' => (string) $site->property_id,
            'timestamp' => time(),
        ];

        $headers = $this->authHeaders($payload);

        try {
            $response = Http::withHeaders($headers)
                ->attach(
                    'file',
                    file_get_contents($jsonPath),
                    $site->property_id.'_ga_service_account.json'
                )
                ->asMultipart()
                ->post("$apiHost/upload-credential", [
                    'property_id' => (string) $site->property_id,
                    'force_update' => 1,
                ]);

            if ($response->status() === 409) {
                $this->warn("Credential already exists in API for {$site->property_id}. Skipping upload.");
                return true;
            }

            return $response->successful();
        } catch (\Exception $e) {
            $this->error("Exception during credential upload: ".$e->getMessage());
            return false;
        }
    }

    protected function callPullAPI(string $apiHost, $site): bool
    {
        $payload = [
            'property_id' => (string) $site->property_id,
            'days' => 365,
            'timestamp' => time(),
        ];

        $headers = $this->authHeaders($payload);

        $response = Http::withHeaders($headers)->post("$apiHost/pull", $payload);

        return $response->successful();
    }

    protected function callTrainAPI(string $apiHost, $site): bool
    {
        $payload = [
            'property_id' => (string) $site->property_id,
            'days' => 730,
            'timestamp' => time(),
        ];

        $headers = $this->authHeaders($payload);

        $response = Http::withHeaders($headers)->post("$apiHost/train", $payload);

        return $response->successful();
    }
}
