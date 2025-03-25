<?php
namespace wildcats1369\Filametrics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\AnalyticsClientFactory;
class FilametricsSite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'domain_name',
        'visibility',
        'view_id',
    ];

    public $account_forms;


    public function accounts(): HasMany
    {
        return $this->hasMany(FilametricsAccount::class, 'site_id');
    }

    public function getAccountForms()
    {

        foreach ($this->accounts as $account) {
            $account_forms[$account['provider']]['provider'] = $account['provider'];
            $account_forms[$account['provider']][$account['name']] = $account['value'];
        }
        $this->account_forms = $account_forms;
        return $account_forms;
    }

    public function getGoogleAnalytics()
    {
        $config = array_merge($this->getAccountForms()['google'], config('filametrics'));
        $config['service_account_credentials_json'] = storage_path('app/private/'.$config['service_account_credentials_json']);
        $client = AnalyticsClientFactory::createForConfig($config);

        return new Analytics($client, $config['property_id']);

    }
}
