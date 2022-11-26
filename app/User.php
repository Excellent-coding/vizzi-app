<?php

namespace App;

use App\Notifications\IAPHSDayOneEmail;
use App\Notifications\ResetPassword;
use App\Notifications\OneDay;
use App\Notifications\OneDayVerify;
use App\Notifications\OneHour;
use App\Notifications\VerifyEmail;
use App\Notifications\WelcomeEmail;
use App\Notifications\VerifyEmailResend;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;
use App\Traits\ConvertDateToUserTimeZone;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use ConvertDateToUserTimeZone;
    
    public $resend_verify = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'role',
        'status',
        'company',
        'title',
        'phone',
        'address',
        'zipcode',
        'parent',
        'type',
        'verify_code',
        'domain_id',
        'booth_id',
        'timezone',
        'user_timezone_region',
        'user_timezone',
        'register_metadata',
        'bio'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'photo_url',
        'created_at',
    ];

    public static function findOrNew($data) {
        $user = self::where('email', $data['email'])->first();
        if (!empty($user)) {
            if ($user->update($data)) {
                return $user;
            } else {
                return false;
            }
        } else {
            return self::create($data);
        }
    }

    /**
     * Get the profile photo URL attribute.
     *
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
    }

    /**
     * Get the oauth providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token -- this parameter is discarded, 
     * need to use pre-set user auth token field
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        if ($this->resend_verify) {
            $this->notify(new VerifyEmailResend);
        } else {
            $this->notify(new VerifyEmail);
        }
    }

    /**
     * Send the one day reminder notification.
     *
     * @return void
     */
    public function sendOneDayNotification()
    {
        if ($this->resend_verify) {
            $this->notify(new OneDayVerify);
        } else {
            $this->notify(new OneDay);
        }
    }

    /**
     * Send welcome notification.
     *
     * @return void
     */
    public function sendWelcomeEmail()
    {
        $this->notify(new WelcomeEmail($this));
    }

	/**
     * Send IAPHS Day One Email.
     *
     * @return void
     */
    public function sendIAPHSDayOne()
    {
        $this->notify(new IAPHSDayOneEmail($this));
    }

    /**
     * Send the one hour reminder notification.
     *
     * @return void
     */
    public function sendOneHourNotification()
    {
        $this->notify(new OneHour);
    }
    /**
     * Send the one hour reminder notification.
     *
     * @return void
     */
    public function sendAgendaNotification()
    {
        $this->notify(new Agenda);
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function domain()
    {
        return $this->hasMany('App\Models\Domain', 'user_id');
    }

    public function domainMember()
    {
        return $this->hasOne('App\Models\Domain', 'id', 'user_id');
    }

    public function parentUser()
    {
        return $this->hasMany('App\User', 'parent', 'id');
    }

    public function adult()
    {
        return $this->hasOne('App\User', 'id', 'parent');
    }

    public function booth()
    {
        return $this->hasOne('App\Models\Booth', 'id', 'booth_id');
    }

    public function poster()
    {
        return $this->hasOne('App\Models\Poster', 'id', 'booth_id');
    }

    const TZ_REGIONS = array(
        \DateTimeZone::AFRICA => "Africa",
        \DateTimeZone::AMERICA => "America",
        \DateTimeZone::ANTARCTICA => "Antarctica",
        \DateTimeZone::ASIA => "Asia",
        \DateTimeZone::ATLANTIC => "Atlantic",
        \DateTimeZone::AUSTRALIA => "Australia",
        \DateTimeZone::EUROPE => "Europe",
        \DateTimeZone::INDIAN => "Indian",
        \DateTimeZone::PACIFIC => "Pacific",
    );


    const TZ_REGION_SYSTEM_US = 1024;

    const TZ_REGION_DEFAULT = \DateTimeZone::AMERICA;
    const TIMEZONE_DEFAULT = 'America/Denver';

    // todo -- document these... PJT
    public function tzDefaults() {
        if (empty($this->user_timezone)) {
            $this->user_timezone = Users::TIMEZONE_DEFAULT;
        }
        if (empty($this->user_timezone_region)) {
            $this->user_timezone_region = Users::TZ_REGION_DEFAULT;
        }        
    }

    public function userNow() {
        $useDate = new \DateTime();
        if (!empty($this->user_timezone)) {
            $userTz = new \DateTimeZone($this->user_timezone);

            $useDate->setTimezone($userTz);
        } else {
            $tz = new \DateTimeZone(self::TIMEZONE_DEFAULT);

            $useDate->setTimezone($tz);
        }        

        return $useDate;
    }

    public function userTime($useDate) {
        if (!empty($this->user_timezone)) {
            $userTz = new \DateTimeZone($this->user_timezone);

            $useDate->setTimezone($userTz);
        } else {
            $tz = new \DateTimeZone(self::TIMEZONE_DEFAULT);

            $useDate->setTimezone($tz);
        }        

        return $useDate;
    }

    public function userTimeZone() {
        if (!empty($this->user_timezone)) {
            $userTz = new \DateTimeZone($this->user_timezone);
        } else {
            $userTz = new \DateTimeZone(self::TIMEZONE_DEFAULT);
        }
        return $userTz;
    }
    public function userTimeToDB($useDate) {
        $utcTz = new \DateTimeZone(self::TIMEZONE_DEFAULT);

        $useDate->setTimezone($utcTz);

        return $useDate;
    }

    public function setAuthToken() {
        $var = \Illuminate\Support\Str::random(32);
        $this->token = $var;
        $this->save();
    }


}