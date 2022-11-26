<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Poster extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = ['domain_id', 'category_id', 'title', 'user_id', 'entry_id'];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }

    public function header()
    {
        return $this->hasMany('App\Models\Header', 'booth_id', 'id')
                ->where('status', 1)
                ->where('type', 'poster')
                ->orderBy('order', 'asc');
    }
}
