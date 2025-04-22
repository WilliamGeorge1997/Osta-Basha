<?php

namespace Modules\Client\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Country\App\Models\City;
use Modules\Client\App\Models\Phone;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'client_id',
        'city_id',
        'latitude',
        'longitude',
        'block',
        'street',
        'house_number',
        'notes',
        'default',
        'phone_id', 
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Address')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    //Serialize Dates
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
