<?php

namespace Modules\Provider\Service;

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\File;
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
            ->where('type', 'service_provider')
            ->whereHas('providerProfile', function ($query) use ($data) {
                $query
                    ->when($data['sub_category_id'] ?? null, function ($q) use ($data) {
                        $q->where('sub_category_id', $data['sub_category_id']);
                    })
                    ->active()
                    ->withinActiveSubscriptionPeriod();

            })
            ->with($relations);
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
            ->with($relations)
            ->withCount('providerContacts as contacts_count')
            ->orderByDesc('contacts_count')
            ->take(10);
        return getCaseCollection($providers, $data);
    }
}