<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Booth extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $table = 'booths';

    protected $fillable = ['user_id', 'domain_id', 'title', 'type', 'hall_id'];

    public function template()
    {
        return $this->hasOne('App\Models\Template', 'id', 'template_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function members()
    {
        return $this->hasMany('App\User', 'booth_id', 'id');
    }

    public function header()
    {
        return $this->hasMany('App\Models\Header', 'booth_id', 'id')
                ->where('status', 1)
                ->where('type', '<>', 'poster')
                ->orderBy('order', 'asc');
    }
}
