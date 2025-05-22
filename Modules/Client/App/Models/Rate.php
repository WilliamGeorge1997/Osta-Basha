<?php

namespace Modules\Client\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\User\App\Models\User;

class Rate extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['client_id', 'rateable_id', 'rateable_type', 'rate'];
    protected $hidden = ['rateable_id', 'rateable_type'];
    //Log Activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Rate')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
    //Serialize Dates
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }
    //Relations
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function rateable()
    {
        return $this->morphTo();
    }
}
