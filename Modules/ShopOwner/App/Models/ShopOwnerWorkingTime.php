<?php

namespace Modules\ShopOwner\App\Models;

use Modules\User\App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ShopOwner\Database\factories\ShopOwnerWorkingTimeFactory;

class ShopOwnerWorkingTime extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'day',
        'start_at',
        'end_at',
    ];
    //Serialize Dates
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
