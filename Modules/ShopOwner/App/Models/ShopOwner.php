<?php

namespace Modules\ShopOwner\App\Models;

use Modules\User\App\Models\User;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Category\App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopOwner extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'sub_category_id', 'shop_name', 'products_description', 'address', 'start_date', 'end_date', 'status', 'is_active', 'experience_years', 'price'];

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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
