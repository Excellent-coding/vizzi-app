<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Location extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = ['name'];
}
