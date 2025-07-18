<?php

namespace Modules\Provider\App\Models;

use Modules\User\App\Models\User;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Modules\Package\App\Models\Package;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Category\App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'sub_category_id', 'card_number', 'card_image', 'address', 'experience_years', 'experience_description', 'price', 'unit', 'start_date', 'end_date', 'is_active', 'status', 'package_id'];
    protected $hidden = ['password'];
    //Log Activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Provider_Profile')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    //Serialize Dates
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    //Get FullImage Path
    public function getCardImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/provider/' . $value);
            }
        }
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
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
