<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Hall extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = [
        'domain_id', 'user_id', 'type', 'name', 'status', 'categories'
    ];

    public function boothItems()
    {
        return $this->hasMany('App\Models\Booth', 'hall_id', 'id');
    }
}
