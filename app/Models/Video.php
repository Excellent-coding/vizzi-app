<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;
class Video extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = [
        'domain_id',
        'session_id',
        'title',
        'format',
        'kind',
        'link',
        'description',
        'hashtag',
        'presenter',
        'isChat',
        'isNote',
        'code',
        'status',
        'stream_url',
        'stream_name',
        'stream_active',
        'zoom_response'
    ];

    public function session()
    {
        return $this->hasOne('App\Models\Session', 'id', 'session_id');
    }
}
