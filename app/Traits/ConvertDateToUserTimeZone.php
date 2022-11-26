<?php

namespace App\Traits;
use Carbon\Carbon;

trait ConvertDateToUserTimeZone {

    public function getCreatedAtAttribute($value)
    {
        $user = auth()->user();
        $created_at_utc = Carbon::parse($value);
        if ($user->timezone && $created_at_utc) {
            $dateWithUserTimezone = Carbon::createFromFormat('Y-m-d H:i:s', $created_at_utc);
            $dateWithUserTimezone = $dateWithUserTimezone->setTimezone($user->timezone);
            return Carbon::createFromFormat('Y-m-d H:i:s', $dateWithUserTimezone);
        }
        return $created_at_utc;
    }

    public function getUpdatedAtAttribute($value)
    {
        $user = auth()->user();
        $updated_at_utc = Carbon::parse($value);
        if ($user->timezone && $updated_at_utc) {
            $dateWithUserTimezone = Carbon::createFromFormat('Y-m-d H:i:s', $updated_at_utc);
            $dateWithUserTimezone = $dateWithUserTimezone->setTimezone($user->timezone);
            return Carbon::createFromFormat('Y-m-d H:i:s', $dateWithUserTimezone);
        }
        return $updated_at_utc;
    }
}