<?php

namespace wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;

use Filament\Resources\Pages\EditRecord;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;
use Log;
use wildcats1369\Filametrics\Models\FilametricsAccount;
use wildcats1369\Filametrics\Models\FilametricsSite;
use Filament\Pages\Actions\Action;


class EditFilametricsSite extends EditRecord
{
    protected static string $resource = FilametricsSiteResource::class;

    public $account_forms;
    public function mount($record): void
    {
        parent::mount($record);
        $site = FilametricsSite::where('id', $record)->get()->toArray()[0];

        $accounts = FilametricsAccount::where('site_id', $record)->get()->toArray();
        $this->accounts = $accounts;
        $providers = [];
        foreach ($accounts as $account) {
            $this->account_forms[$account['provider']]['provider'] = $account['provider'];
            $this->account_forms[$account['provider']][$account['name']] = $account['value'];
            $providers[] = $account['provider'];
        }

        $this->form->fill([
            "id" => $site['id'],
            "user_id" => $site['user_id'],
            "domain_name" => $site['domain_name'],
            "visibility" => $site['visibility'],
            "view_id" => $site['view_id'],
            "created_at" => $site['created_at'],
            "updated_at" => $site['updated_at'],
            'account_forms' => $this->account_forms,
            'providers' => array_unique($providers),
        ]);


    }

    protected function beforeSave(): void
    {

        $transformedAccounts = [];
        $data = $this->form->getState();
        Log::info('beforSave:', $data);
        foreach ($data['account_forms'] as $account) {
            if ($account['provider'] === 'google') {
                $transformedAccounts[] = [
                    'name' => 'property_id',
                    'label' => 'Property ID',
                    'type' => 'text',
                    'value' => $account['property_id'],
                    'provider' => $account['provider'],
                    'site_id' => $this->record->id,
                ];
                $transformedAccounts[] = [
                    'name' => 'service_account_credentials_json',
                    'label' => 'Service Account Credentials JSON',
                    'type' => 'upload',
                    'value' => $account['service_account_credentials_json'],
                    'provider' => $account['provider'],
                    'site_id' => $this->record->id,
                ];
            } elseif ($account['provider'] === 'moz') {
                $transformedAccounts[] = [
                    'name' => 'api_id',
                    'label' => 'API ID',
                    'type' => 'text',
                    'value' => $account['api_id'],
                    'provider' => $account['provider'],
                    'site_id' => $this->record->id,
                ];
            }
        }
        foreach ($transformedAccounts as $transformedAccount) {
            $account = FilametricsAccount::where('site_id', $this->record->id)
                ->where('name', $transformedAccount['name'])
                ->where('provider', $transformedAccount['provider'])->first();
            Log::info("account: ".json_encode($account));
            if (isset($account->id)) {
                $account->save($transformedAccount);
                Log::info("Updating account: ".json_encode($transformedAccount));
            } else {
                Log::info("Creating account: ".json_encode($transformedAccount));
                FilametricsAccount::create($transformedAccount);
            }
        }

    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->label('View')
                ->action(function () {
                    return redirect()->route('filament.admin.resources.filametrics-sites.view', ['record' => $this->record->id]);
                })
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->button(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record->id]);
    }


}
