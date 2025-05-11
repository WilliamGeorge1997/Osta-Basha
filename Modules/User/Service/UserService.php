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
    public function completeRegistration($type, $user, $userDetailsData, $profileData, $workingTimesData)
    {
        if (request()->hasFile('image')) {
            $userDetailsData['image'] = $this->upload(request()->file('image'), 'user');
        }
        $user->update($userDetailsData);

        switch ($type) {
            case 'service_provider':
                $this->completeProviderRegistration($user, $profileData, $workingTimesData);
                return $user->fresh();

            case 'shop_owner':
                $this->completeShopOwnerRegistration($user, $profileData, $workingTimesData);
                return $user->fresh();

            default:
                return $user->fresh();
        }
    }

    private function completeProviderRegistration($user, $profileData, $workingTimesData)
    {
        if (request()->hasFile('card_image')) {
            $profileData['card_image'] = $this->upload(request()->file('card_image'), 'provider');
        }
        $providerProfile = $user->providerProfile()->create($profileData);
        $user->providerWorkingTimes()->createMany($workingTimesData);
        $this->processCertificates($user, 'certificates', 'provider/certificates', 'providerCertificates');
    }

    private function completeShopOwnerRegistration($user, $profileData, $workingTimesData)
    {
        if (request()->hasFile('card_image')) {
            $profileData['card_image'] = $this->upload(request()->file('card_image'), 'shop_owner');
        }
        $shopOwnerProfile = $user->shopOwnerProfile()->create($profileData);
        $user->shopOwnerWorkingTimes()->createMany($workingTimesData);
        $this->processCertificates($user, 'certificates', 'shop_owner/certificates', 'shopOwnerCertificates');
    }

    private function processCertificates($user, $requestKey, $uploadPath, $relationMethod)
    {
        if (!request()->has($requestKey)) {
            return;
        }
        $certificates = collect(request()->file($requestKey))->map(function ($certificate) use ($uploadPath) {
            return [
                'certificate_image' => $this->upload($certificate, $uploadPath)
            ];
        })->toArray();
        if (!empty($certificates)) {
            $user->$relationMethod()->createMany($certificates);
        }
    }


    public function verifyOtp($data)
    {
        $user = $this->findBy('phone', $data['phone'])[0];
        if ($user && $user->verify_code == $data['otp']) {
            $this->update($user->id, ['is_active' => 1]);
            return $user;
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
