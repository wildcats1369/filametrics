<?php
namespace wildcats1369\Filametrics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
