<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Header extends Model
{
    use ConvertDateToUserTimeZone;
    
    public function tab()
    {
        return $this->hasMany('App\Models\Tab', 'header_id', 'id');
    }
}
