<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Event extends Model
{
  use ConvertDateToUserTimeZone;
  
    protected $fillable = ['domain_id', 'start', 'end', 'timezone', 'date'];

    public function session()
    {
        return $this->hasMany('App\Models\Session', 'event_id', 'id')->orderBy('start', 'asc');
    }

    public function sessions() {
      $sessionData = Session::where('event_id', $this->id)
        ->with('track')
        ->with('event')
        ->orderBy('event_id', 'asc')
        ->get();

      return $sessionData;
    }

    public function user()
    {
        return $this->hasMany('App\User', 'domain_id', 'domain_id');
    }

    public function domain()
    {
        return $this->hasOne('App\Models\Domain', 'id', 'domain_id');
    }

}
