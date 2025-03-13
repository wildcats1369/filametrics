<?php
namespace wildcats1369\Filametrics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilametricsAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'name',
        'label',
        'type',
        'provider',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(FilametricsSite::class, 'site_id'); // Ensure foreign key matches
    }
}

