<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Tab extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = ['index', 'header_id', 'title'];

    public function media()
    {
        return $this->hasMany('App\Models\Upload', 'tab_id', 'id');
    }
}
