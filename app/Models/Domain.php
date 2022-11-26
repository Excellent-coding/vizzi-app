<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Domain extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $table = 'domains';

    protected $fillable = ['user_id', 'domain', 'title'];

    public function admin()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function user()
    {
        return $this->hasMany('App\User', 'domain_id', 'id');
    }

    public function video()
    {
        return $this->hasMany('App\Models\VideoPage', 'domain_id', 'id');
    }
}
