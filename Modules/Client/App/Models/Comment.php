<?php

namespace Modules\Client\App\Models;

use Modules\User\App\Models\User;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['client_id', 'commentable_id', 'commentable_type', 'comment'];
    protected $hidden = ['commentable_id', 'commentable_type'];
   //Log Activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Comment')
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
    public function commentable()
    {
        return $this->morphTo();
    }
}
