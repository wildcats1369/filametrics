<?php
namespace wildcats1369\Filametrics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilametricsSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'view_id',
        'service_account_credentials_json',
    ];
}
