<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;
class Track extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = [
        'id',
         'domain_id',
         'category_id',
         'location_id',
         'title',
         'description',
         'color',
         'status',
         'hashtag'
     ];


    // todo: should have track, day along with title here
    public static function findOrNew($data) {
        $exists = self::where('title', $data['title'])->first();
        if ($exists) return $exists;
        else {
            $exists = self::create($data);
        }

        return $exists;
    }

    public function location()
    {
        return $this->hasOne('App\Models\Location', 'id', 'location_id');
    }

    public function category()
    {
        return $this->hasOne('App\Models\TrackCategory', 'id', 'category_id');
    }

    public function session()
    {
        return $this->hasMany('App\Models\Session', 'track_id', 'id');
    }

}
