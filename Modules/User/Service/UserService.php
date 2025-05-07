<?php

namespace Modules\User\Service;

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Modules\Common\Helpers\UploadHelper;


class UserService
{
    use UploadHelper;
    function findAll($data, $relation)
    {
        $users = User::query()->with($relation);
        return getCaseCollection($users, $data);
    }

    function findById($id)
    {
        return User::find($id);
    }
    function findBy($key, $value)
    {
        return User::where($key, $value)->get();
    }
    public function create($data)
    {
        if (request()->hasFile('image')) {
            $data['image'] = $this->upload(request()->file('image'), 'user');
        }
        $user = User::create($data);
        return $user;
    }

    public function verifyOtp($data)
    {
        $user = $this->findBy('phone', $data['phone'])[0];
        if ($user && $user->verify_code == $data['otp']) {
            return $this->update($user->id, ['is_active' => 1]);
        }
        return false;
    }

    function update($id, $data)
    {
        $user = $this->findById($id);
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/User/' . $this->getImageName('user', $user->image)));
            $data['image'] = $this->upload(request()->file('image'), 'client');
        }
        $user->update($data);
        return $user;
    }

    public function changePassword($data)
    {
        $user = auth('user')->user();
        $user->update([
            'password' => Hash::make($data['new_password'])
        ]);
    }

    public function updateProfile($data)
    {
        $user = auth('user')->user();
        if (request()->hasFile('image')) {
            if ($user->image) {
                File::delete(public_path('uploads/user/' . $this->getImageName('user', $user->image)));
            }
            $data['image'] = $this->upload(request()->file('image'), 'user');
        }
        $user->update($data);
    }
}