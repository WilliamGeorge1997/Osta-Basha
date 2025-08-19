<?php

namespace Modules\Admin\App\Models;

use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Restaurant\App\Models\Restaurant;
use Modules\Notification\App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'email', 'phone', 'password', 'image', 'is_active', 'expo_token'];
    protected $hidden = ['password', 'remember_token'];


    //Log Activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Admin')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    //Serialize Dates
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    //Get Full Image Path
    public function getImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/admin/' . $value);
            }
        }
    }
    public function routeNotificationForExpoPushNotifications()
    {
        $tableName = config('exponent-push-notifications.interests.database.table_name');

        $token = DB::table($tableName)
            ->where('model', self::class)
            ->where('key', (string) $this->id)
            ->value('value');

        return $token ?: $this->expo_token;
    }
    public function routeNotificationFor($driver)
    {
        if ($driver === 'ExpoPushNotifications') {
            return (string) $this->id;
        }
        return null;
    }
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->orWhere('notifiable_id', null);
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