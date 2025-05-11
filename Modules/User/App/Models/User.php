<?php

namespace Modules\User\App\Models;

use Spatie\Activitylog\LogOptions;
use Modules\Service\App\Models\Service;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Modules\Provider\App\Models\Provider;
use Modules\ShopOwner\App\Models\ShopOwner;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Client\App\Models\ClientProviderContact;
use Modules\Provider\App\Models\ProviderCertificate;
use Modules\Provider\App\Models\ProviderWorkingTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ShopOwner\App\Models\ShopOwnerCertificate;
use Modules\ShopOwner\App\Models\ShopOwnerWorkingTime;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'type', 'password', 'image', 'verify_code', 'is_active', 'fcm_token'];
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
    // ----------------------Provider--------------------------------

    public function providerProfile()
    {
        return $this->hasOne(Provider::class);
    }

    public function providerWorkingTimes()
    {
        return $this->hasMany(ProviderWorkingTime::class);
    }

    public function providerCertificates()
    {
        return $this->hasMany(ProviderCertificate::class);
    }
    public function service()
    {
        return $this->hasOne(Service::class);
    }

    // ---------------------- Shop Owner --------------------------------

    public function shopOwnerProfile()
    {
        return $this->hasOne(ShopOwner::class);
    }

    public function shopOwnerWorkingTimes()
    {
        return $this->hasMany(ShopOwnerWorkingTime::class);
    }

    public function shopOwnerCertificates()
    {
        return $this->hasMany(ShopOwnerCertificate::class);
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
