<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;
class TrackCategory extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = ['name'];
}
