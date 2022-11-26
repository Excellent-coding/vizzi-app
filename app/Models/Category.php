<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Category extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = ['user_id', 'domain_id', 'category'];

    public function backdrop()
    {
        return $this->hasOne('App\Models\PosterBackdrop', 'id', 'bg');
    }

    public function posterItems()
    {
        return $this->hasMany('App\Models\Poster', 'category_id', 'id');
    }
}
