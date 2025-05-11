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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
