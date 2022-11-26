<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class PollAnswers extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $fillable = ['poll_id', 'user_id', 'answer'];
}
