<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Template extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = ['domain_id', 'user_id', 'type', 'media'];
}
