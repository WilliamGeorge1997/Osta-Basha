<?php

namespace Modules\Admin\Service;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Modules\Common\Helpers\UploadHelper;

class AdminService
{
    use UploadHelper;

    public function changePassword($data)
    {
        $admin = auth('admin')->user();
        $admin->update([
            'password' => Hash::make($data['new_password'])
        ]);
    }

    public function updateProfile($data)
    {
        $admin = auth('admin')->user();
        if (request()->hasFile('image')) {
            if ($admin->image) {
                File::delete(public_path('uploads/admin/' . $this->getImageName('admin', $admin->image)));
            }
            $data['image'] = $this->upload(request()->file('image'), 'admin');
        }
            $admin->update($data);
    }
}
