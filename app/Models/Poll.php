<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class Poll extends Model
{
    use ConvertDateToUserTimeZone;

    protected $fillable = ['title', 'answers', 'expire', 'active'];

    protected $casts = [
        'answers' => 'array',
    ];

    public function answers() {
        return $this->hasMany(PollAnswers::class);
    }
}
