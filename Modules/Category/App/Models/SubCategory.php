<?php

namespace Modules\Category\App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Modules\Category\App\Models\Category;
use Modules\Provider\App\Models\Provider;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Category\App\Models\SubCategoryLocalization;

class SubCategory extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['title_ar', 'title_en', 'description_ar', 'description_en', 'image', 'category_id', 'is_active'];
    //Log Activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('SubCategory')
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
                return asset('uploads/sub_category/' . $value);
            }
        }
    }

    //Helper
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    //Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'provider_sub_category')
            ->withTimestamps();
    }
    public function localizations()
    {
        return $this->hasMany(SubCategoryLocalization::class);
    }
}
