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
            case User::TYPE_CLIENT:
                return $user->fresh();

            case User::TYPE_SERVICE_PROVIDER:
                $this->completeProviderRegistration($user, $profileData, $workingTimesData);
                if (!empty($user->city) && !empty($user->country)) {
                    \Modules\User\App\Jobs\NotifyClientsAboutNewServiceJob::dispatch(
                        $user->id,
                        User::TYPE_SERVICE_PROVIDER,
                        $user->city,
                        $user->country
                    )->onConnection('database');
                }
                return $user->fresh()->load('providerProfile', 'providerWorkingTimes', 'providerCertificates', 'providerProfile.package', 'providerContacts.client');

            case User::TYPE_SHOP_OWNER:
                $this->completeShopOwnerRegistration($user, $profileData, $workingTimesData);
                if (!empty($user->city) && !empty($user->country)) {
                    \Modules\User\App\Jobs\NotifyClientsAboutNewServiceJob::dispatch(
                        $user->id,
                        User::TYPE_SHOP_OWNER,
                        $user->city,
                        $user->country
                    )->onConnection('database');
                }
                return $user->fresh()->load('shopOwnerProfile', 'shopOwnerWorkingTimes', 'shopOwnerShopImages', 'shopOwnerProfile.package', 'shopOwnerContacts.client');

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
        $this->processImages($user, 'certificates', 'provider/certificates', 'providerCertificates');
    }

    private function completeShopOwnerRegistration($user, $profileData, $workingTimesData)
    {
        $shopOwnerProfile = $user->shopOwnerProfile()->create($profileData);
        $user->shopOwnerWorkingTimes()->createMany($workingTimesData);
        $this->processImages($user, 'shop_images', 'shop_owner/shop_images', 'shopOwnerShopImages');
    }

    private function processImages($user, $requestKey, $uploadPath, $relationMethod)
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

    public function updateProfile($type, $user, $userDetailsData, $profileData, $workingTimesData)
    {
        if (request()->hasFile('image')) {
            if ($user->image) {
                File::delete(public_path('uploads/user/' . $this->getImageName('user', $user->image)));
            }
            $userDetailsData['image'] = $this->upload(request()->file('image'), 'user');
        }
        $user->update($userDetailsData);

        switch ($type) {
            case User::TYPE_CLIENT:
                return $user->fresh();

            case User::TYPE_SERVICE_PROVIDER:
                $this->updateProviderProfile($user, $profileData, $workingTimesData);
                return $user->fresh()->load('providerProfile.package', 'providerWorkingTimes', 'providerCertificates', 'providerContacts.client');

            case User::TYPE_SHOP_OWNER:
                $this->updateShopOwnerProfile($user, $profileData, $workingTimesData);
                return $user->fresh()->load('shopOwnerProfile.package', 'shopOwnerWorkingTimes', 'shopOwnerShopImages', 'shopOwnerContacts.client');

            default:
                return $user->fresh();
        }
    }
    private function updateProviderProfile($user, $profileData, $workingTimesData)
    {
        if (request()->hasFile('card_image')) {
            if ($user->providerProfile->card_image) {
                File::delete(public_path('uploads/provider' . $this->getImageName('provider', $user->providerProfile->card_image)));
            }
            $profileData['card_image'] = $this->upload(request()->file('card_image'), 'provider');
        }
        $user->providerProfile()->update($profileData);
        $user->providerWorkingTimes()->delete();
        $user->providerWorkingTimes()->createMany($workingTimesData);

        // foreach ($workingTimesData as $workingTime) {
        //     if (isset($workingTime['id'])) {
        //         $user->providerWorkingTimes()->where('id', $workingTime['id'])->update([
        //             'day' => $workingTime['day'],
        //             'start_at' => $workingTime['start_at'],
        //             'end_at' => $workingTime['end_at']
        //         ]);
        //     } else {
        //         $user->providerWorkingTimes()->create([
        //             'user_id' => $workingTime['user_id'],
        //             'day' => $workingTime['day'],
        //             'start_at' => $workingTime['start_at'],
        //             'end_at' => $workingTime['end_at']
        //         ]);
        //     }
        // }
        $this->processUpdateImages($user, 'certificates', 'provider/certificates', 'providerCertificates');
    }

    private function updateShopOwnerProfile($user, $profileData, $workingTimesData)
    {
        $user->shopOwnerProfile()->update($profileData);
        $user->shopOwnerWorkingTimes()->delete();
        $user->shopOwnerWorkingTimes()->createMany($workingTimesData);


        // foreach ($workingTimesData as $workingTime) {
        //     if (isset($workingTime['id'])) {
        //         $user->shopOwnerWorkingTimes()->where('id', $workingTime['id'])->update([
        //             'day' => $workingTime['day'],
        //             'start_at' => $workingTime['start_at'],
        //             'end_at' => $workingTime['end_at']
        //         ]);
        //     } else {
        //         $user->shopOwnerWorkingTimes()->create([
        //             'user_id' => $workingTime['user_id'],
        //             'day' => $workingTime['day'],
        //             'start_at' => $workingTime['start_at'],
        //             'end_at' => $workingTime['end_at']
        //         ]);
        //     }
        // }
        $this->processUpdateImages($user, 'shop_images', 'shop_owner/shop_images', 'shopOwnerShopImages');
    }

    private function processUpdateImages($user, $requestKey, $uploadPath, $relationMethod)
    {
        if (!request()->has($requestKey)) {
            return;
        }
        $userImages = $user->$relationMethod()->get();
        foreach ($userImages as $image) {
            File::delete(public_path('uploads/' . $uploadPath . '/' . $this->getImageName($uploadPath, $image->image)));
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
    function search($data)
    {
        if (empty($data['query'] ?? null)) {
            return collect();
        }

        $query = User::query()
            ->whereIn('type', [User::TYPE_SERVICE_PROVIDER, User::TYPE_SHOP_OWNER])
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->when(!empty($data['query']), function ($q) use ($data) {
                $searchTerm = '%' . $data['query'] . '%';
                return $q->where(function ($query) use ($searchTerm) {
                    $query->where('first_name', 'like', $searchTerm)
                        ->orWhere('last_name', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhere('phone', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT(country_code, phone) LIKE ?", ['%' . $searchTerm . '%']);
                });
            })
            ->where(function ($q) {
                $q->where(function ($query) {
                    $query->where('type', User::TYPE_SERVICE_PROVIDER)
                        ->whereHas('providerProfile', function ($subquery) {
                            $subquery->where('is_active', 1);
                        });
                })
                    ->orWhere(function ($query) {
                        $query->where('type', User::TYPE_SHOP_OWNER)
                            ->whereHas('shopOwnerProfile', function ($subquery) {
                                $subquery->where('is_active', 1);
                            });
                    });
            })
            ->with([
                'providerProfile' => function ($q) {
                    $q->whereHas('user', function ($query) {
                        $query->where('type', User::TYPE_SERVICE_PROVIDER);
                    })->with('subCategory.category');
                }
            ])
            ->with([
                'shopOwnerProfile' => function ($q) {
                    $q->whereHas('user', function ($query) {
                        $query->where('type', User::TYPE_SHOP_OWNER);
                    })->with('subCategory.category');
                }
            ])
            ->latest();

        return getCaseCollection($query, $data);
    }

    function deleteImage($id)
    {
        $user = auth('user')->user();
        $type = $user->type;
        $deleted = false;

        if ($type == User::TYPE_SERVICE_PROVIDER) {
            $image = $user->providerCertificates()->find($id);
            if (!$image) {
                return false;
            }
            File::delete(public_path('uploads/provider/certificates/' . $this->getImageName('provider/certificates', $image->image)));
            $image->delete();
            $deleted = true;
        } elseif ($type == User::TYPE_SHOP_OWNER) {
            $image = $user->shopOwnerShopImages()->find($id);
            if (!$image) {
                return false;
            }
            File::delete(public_path('uploads/shop_owner/shop_images/' . $this->getImageName('shop_owner/shop_images', $image->image)));
            $image->delete();
            $deleted = true;
        }
        if ($deleted) {
            $relations = [];
            if ($type == User::TYPE_SERVICE_PROVIDER) {
                $relations = array_merge($relations, [
                    'providerProfile',
                    'providerCertificates',
                    'providerWorkingTimes',
                    'providerProfile.package',
                    'providerContacts.client',

                ]);
            } elseif ($type == User::TYPE_SHOP_OWNER) {
                $relations = array_merge($relations, [
                    'shopOwnerProfile',
                    'shopOwnerShopImages',
                    'shopOwnerWorkingTimes',
                    'shopOwnerProfile.package',
                    'shopOwnerContacts.client'
                ]);
            }
            $user = User::with($relations)->find($user->id);
            return $user;
        }

        return false;
    }

    function toggleActivate($user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return $user->fresh();
    }

    function toggleAvailable()
    {
        $user = auth('user')->user();
        $user->update(['is_available' => !$user->is_available]);
        return $user->fresh();
    }

    function getReceivedContacts($data)
    {
        $user = auth('user')->user();
        $type = $user->type;
        if ($type == User::TYPE_SERVICE_PROVIDER) {
            $relation = 'providerContacts';
        } elseif ($type == User::TYPE_SHOP_OWNER) {
            $relation = 'shopOwnerContacts';
        }
        $contacts = $user->$relation()->with('client')->latest();
        return getCaseCollection($contacts, $data);
    }

    function updateLocation($data)
    {
        $user = auth('user')->user();
        $user->update($data);
        return $user->fresh();
    }

    function findToken($id)
    {
        return User::where('id', $id)->first()['expo_token'];
    }
}
