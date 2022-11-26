<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class PosterBackdrop extends Model
{
    use ConvertDateToUserTimeZone;
}
