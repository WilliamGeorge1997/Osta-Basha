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

    public function chooseUserType($data, $user)
    {
        switch ($data['type']) {
            case 'client':
                $user->assignRole('Client');
                break;
            case 'service_provider':
                $user->assignRole('Service Provider');
                break;
            case 'shop_owner':
                $user->assignRole('Shop Owner');
                break;
            default:
                throw new \Exception('Invalid user type');
        }
        $user->update($data);
        return $user->fresh();
    }
    public function completeRegistration($type, $user, $userDetailsData, $profileData, $workingTimesData)
    {
        if (request()->hasFile('image')) {
            $userDetailsData['image'] = $this->upload(request()->file('image'), 'user');
        }
        $userDetailsData['completed_registration'] = 1;
        $user->update($userDetailsData);

        switch ($type) {
            case 'client':
                return $user->fresh();

            case 'service_provider':
                $this->completeProviderRegistration($user, $profileData, $workingTimesData);
                return $user->fresh()->load('providerProfile', 'providerWorkingTimes', 'providerCertificates');

            case 'shop_owner':
                $this->completeShopOwnerRegistration($user, $profileData, $workingTimesData);
                return $user->fresh()->load('shopOwnerProfile', 'shopOwnerWorkingTimes', 'shopOwnerShopImages');

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
        $this->processCertificates($user, 'shop_images', 'shop_owner/shop_images', 'shopOwnerShopImages');
    }

    private function processCertificates($user, $requestKey, $uploadPath, $relationMethod)
    {
        if (!request()->has($requestKey)) {
            return;
        }
        $certificates = collect(request()->file($requestKey))->map(function ($certificate) use ($uploadPath) {
            return [
                'image' => $this->upload($certificate, $uploadPath)
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

    function search($data)
    {
        $query = User::query()
            ->where(function ($q) {
                $q->where('type', 'service_provider')
                    ->orWhere('type', 'shop_owner');
            })
            ->when($data['query'] ?? null, function ($q) use ($data) {
                $searchTerm = '%' . $data['query'] . '%';
                $q->where(function ($query) use ($searchTerm) {
                    $query->where('first_name', 'like', $searchTerm)
                        ->orWhere('last_name', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhere('phone', 'like', $searchTerm);
                });
            });

        $query->where(function ($q) {
            $q->where(function ($query) {
                $query->where('type', 'service_provider')
                    ->whereHas('providerProfile', function ($subquery) {
                        $subquery->where('is_active', 1);
                    });
            })
                ->orWhere(function ($query) {
                    $query->where('type', 'shop_owner')
                        ->whereHas('shopOwnerProfile', function ($subquery) {
                            $subquery->where('is_active', 1);
                        });
                });
        })
        ->latest();
        return getCaseCollection($query, $data);
    }
}
