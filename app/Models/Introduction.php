<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Introduction extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = ['user_id', 'domain_id', 'title', 'content'];
}
