<?php

namespace Modules\Notification\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image', 'notifiable_id', 'notifiable_type', 'read_at', 'group_by'];
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function getImageAttribute($value)
    {
        if ($value != null && $value != '') {
            return asset('uploads/notification/' . $value);
        }
        return $value;
    }

}
