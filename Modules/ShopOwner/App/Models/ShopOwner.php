<?php

namespace Modules\ShopOwner\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ShopOwner extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'sub_category_id', 'shop_name', 'products_description', 'address', 'start_date', 'end_date', 'status', 'is_active'];

    //Log Activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('ShopOwner_Profile')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    //Get FullImage Path
    public function getCardImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/shop_owner/' . $value);
            }
        }
    }
    //Serialize Dates
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
    public function scopeWithinActiveSubscriptionPeriod($query)
    {
        return $query->whereNotNull(['start_date', 'end_date'])
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }
}
