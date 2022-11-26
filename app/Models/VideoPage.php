<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;
class VideoPage extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = [
        'domain_id',
        'category_id',
        'track_id',
        'title',
        'date',
        'start',
        'end',
        'format',
        'kind',
        'link',
        'isChat',
        'isNote',
        'description',
        'hashtag',
        'presenter',
        'readMore',
        'code'
    ];

    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }

    public function track()
    {
        return $this->hasOne('App\Models\Track', 'id', 'track_id');
    }
}