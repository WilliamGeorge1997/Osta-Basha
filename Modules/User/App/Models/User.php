<?php

namespace Modules\User\App\Models;

use Modules\User\App\Models\User;
use Spatie\Activitylog\LogOptions;
use Modules\Service\App\Models\Service;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Client\App\Models\ClientProviderContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'password', 'image', 'verify_code', 'is_active'];
    protected $hidden = ['password'];

    //Log Activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('User')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    //Serialize Dates
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    //Get FullImage Path
    public function getImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/user/' . $value);
            }
        }
    }

    //Helper

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    //Relations
    public function providerContacts()
    {
        return $this->hasMany(ClientProviderContact::class, 'provider_id');
    }

    public function clientContacts()
    {
        return $this->hasMany(ClientProviderContact::class, 'client_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->hasOne(Service::class);
    }

    //JWT

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


}
