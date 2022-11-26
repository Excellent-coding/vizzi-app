<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;
class Upload extends Model
{
    use ConvertDateToUserTimeZone;

    protected $table = 'uploads';

    protected $fillable = ['tab_id', 'title', 'order', 'type', 'item', 'page_id'];
}
