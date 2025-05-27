<?php

namespace Modules\Provider\Service;

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Country\App\Models\Country;
use Modules\Common\Helpers\UploadHelper;

class ProviderService
{
    use UploadHelper;

    function findAll($data = [], $relations = [])
    {
        $providers = User::query()
            ->where('type', 'service_provider')
            ->whereHas('providerProfile')
            ->with($relations);
        return getCaseCollection($providers, $data);
    }

    function findById($id)
    {
        return User::find($id);
    }
    function findBy($key, $value)
    {
        return User::where($key, $value)->get();
    }
    function active($data = [], $relations = [])
    {
        $providers = User::query()
            ->when($data['name'] ?? null, function ($query) use ($data) {
                $query->where('first_name', 'like', '%' . $data['name'] . '%')
                    ->orWhere('last_name', 'like', '%' . $data['name'] . '%');
            })
            ->when($data['email'] ?? null, function ($query) use ($data) {
                $query->where('email', 'like', '%' . $data['email'] . '%');
            })
            ->when($data['phone'] ?? null, function ($query) use ($data) {
                $query->where('phone', 'like', '%' . $data['phone'] . '%');
            })
            ->when($data['city'] ?? null, function ($query) use ($data) {
                $query->where('city', $data['city']);
            })
            ->when($data['country'] ?? null, function ($query) use ($data) {
                $query->where('country', $data['country']);
            })
            ->where('type', 'service_provider')
            ->whereHas('providerProfile', function ($query) use ($data) {
                $query
                    ->when($data['sub_category_id'] ?? null, function ($q) use ($data) {
                        $q->where('sub_category_id', $data['sub_category_id']);
                    })
                    ->active()
                    ->withinActiveSubscriptionPeriod();

            })
            ->when(($data['min_price'] ?? null) || ($data['max_price'] ?? null), function ($query) use ($data) {
                $query->whereHas('providerProfile', function ($q) use ($data) {
                    if (isset($data['min_price'])) {
                        $q->where('price', '>=', $data['min_price']);
                    }
                    if (isset($data['max_price'])) {
                        $q->where('price', '<=', $data['max_price']);
                    }
                });
            })
            ->when($data['card_number'] ?? null, function ($query) use ($data) {
                $query->whereHas('providerProfile', function ($q) use ($data) {
                    $q->where('card_number', 'like', '%' . $data['card_number'] . '%');
                });
            })
            ->when($data['experience_years'] ?? null, function ($query) use ($data) {
                $query->whereHas('providerProfile', function ($q) use ($data) {
                    $q->where('experience_years', $data['experience_years']);
                });
            })
            ->with($relations)
            ->when(auth('user')->check(), function ($query) {
                $query->withCount([
                    'providerContacts as is_contacted' => function ($q) {
                        $q->where('client_id', auth('user')->id());
                    }
                ]);
            })
            ->with([
                'rates' => function ($q) {
                    $q->where('rateable_type', \Modules\Provider\App\Models\Provider::class);
                }
            ])
            ->withCount('rates as rates_count')
            ->withAvg('rates as rates_avg', 'rate')
            ->with([
                'comments' => function ($q) {
                    $q->where('commentable_type', \Modules\Provider\App\Models\Provider::class);
                }
            ])
            ->withCount('comments as comments_count')
            ->where('is_available', 1)
            ->where('is_active', 1)
            ->latest();
        return getCaseCollection($providers, $data);
    }


    function update($id, $data)
    {
        $provider = $this->findById($id);
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/Provider/' . $this->getImageName('provider', $provider->image)));
            $data['image'] = $this->upload(request()->file('image'), 'provider');
        }
        $provider->update($data);
        return $provider;
    }

    function updateProfile($data)
    {
        $provider = auth('provider')->user();
        if (request()->hasFile('image')) {
            if ($provider->image) {
                File::delete(public_path('uploads/provider/' . $this->getImageName('provider', $provider->image)));
            }
            $data['image'] = $this->upload(request()->file('image'), 'provider');
        }
        $provider->update($data);
    }

    function mostContactedProviders($data = [], $relations = [])
    {
        $providers = User::query()
            ->when($data['name'] ?? null, function ($query) use ($data) {
                $query->where('first_name', 'like', '%' . $data['name'] . '%')
                    ->orWhere('last_name', 'like', '%' . $data['name'] . '%');
            })
            ->when($data['email'] ?? null, function ($query) use ($data) {
                $query->where('email', 'like', '%' . $data['email'] . '%');
            })
            ->when($data['phone'] ?? null, function ($query) use ($data) {
                $query->where('phone', 'like', '%' . $data['phone'] . '%');
            })
            ->when($data['city'] ?? null, function ($query) use ($data) {
                $query->where('city', $data['city']);
            })
            ->when($data['country'] ?? null, function ($query) use ($data) {
                $query->where('country', $data['country']);
            })
            ->when($data['sub_category_id'] ?? null, function ($query) use ($data) {
                $query->whereHas('providerProfile', function ($q) use ($data) {
                    $q->where('sub_category_id', $data['sub_category_id']);
                });
            })

            ->where('type', 'service_provider')
            ->with($relations)
            ->withCount('providerContacts as contacts_count')
            ->whereHas('providerProfile', function ($query) {
                $query->where('is_active', 1);
            })
            ->withCount('rates as rates_count')
            ->withAvg('rates as rates_avg', 'rate')
            ->withCount('comments as comments_count')
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->orderByDesc('contacts_count');
        return getCaseCollection($providers, $data);
    }

    function updateSubscription($user, $data)
    {
        if (request()->has('is_active') && request()->input('is_active') == 1) {
            $data['is_active'] = 1;
        } else {
            $data['is_active'] = 0;
        }
        $data['status'] = 'subscribed';
        $user->providerProfile->update($data);
        return $user;
    }

    function relatedProviders($data = [], $relations = [])
    {
        $provider_subcategory_id = User::find($data['user_id'])->providerProfile->sub_category_id;
        if ($provider_subcategory_id) {
            $providers = User::query()
                ->where('type', 'service_provider')
                ->where('is_active', 1)
                ->where('is_available', 1)
                ->whereHas('providerProfile', function ($query) use ($provider_subcategory_id) {
                    $query->where('sub_category_id', $provider_subcategory_id);
                })
                ->whereHas('providerProfile', function ($query) {
                    $query->where('is_active', 1);
                })
                ->with($relations)
                ->latest();
        }
        return getCaseCollection($providers, $data);
    }
}
