<?php
namespace wildcats1369\Filametrics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilametricsData extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'account_id',
        'data',
    ];
}
