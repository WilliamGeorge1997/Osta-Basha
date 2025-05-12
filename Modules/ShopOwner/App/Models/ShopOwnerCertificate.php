<?php

namespace Modules\ShopOwner\App\Models;

use Modules\User\App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ShopOwner\Database\factories\ShopOwnerCertificateFactory;

class ShopOwnerCertificate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'certificate_image',
    ];
    //Serialize Dates
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }
    //Get FullImage Path
    public function getCertificateImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/shop_owner/certificates/' . $value);
            }
        }
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
