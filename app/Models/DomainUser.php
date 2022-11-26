<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ConvertDateToUserTimeZone;

class DomainUser extends Model
{
    use ConvertDateToUserTimeZone;
    
    protected $table = 'domain_users';

    protected $fillable = ['user_id', 'domain_id', 'code', 'status'];
}
