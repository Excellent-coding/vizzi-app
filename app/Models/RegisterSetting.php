<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class RegisterSetting extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = ['domain_id', 'label', 'required', 'disabled', 'order', 'user_id'];
}
