<?php
namespace wildcats1369\Filametrics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilametricsSite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'domain_name',
        'visibility',
        'view_id',
    ];


    public function accounts(): HasMany
    {
        return $this->hasMany(FilametricsAccount::class, 'site_id');
    }

    protected static function booted()
    {
        static::saving(function ($site) {
            \Log::info('Saving Filametrics Site:', $site->toArray());
        });
    }
}
