<?php

namespace Modules\Category\App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Modules\Country\App\Models\Country;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubCategoryLocalization extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['sub_category_id', 'country_id', 'title_ar', 'title_en'];

    //Log Activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('SubCategoryLocalization')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    //Serialize Dates
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    //Helper
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    //Relations
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}