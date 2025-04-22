<?php
namespace Modules\Client\App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Modules\Client\App\Models\Client;
use Modules\Client\App\Models\Address;


class Phone extends Model
{
    protected $fillable = ['client_id', 'phone', 'is_verified', 'verify_code'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Phone')
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

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    
}
