<?php

namespace Modules\Provider\Service;

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Modules\Common\Helpers\UploadHelper;
use Modules\Provider\App\Models\Provider;

class ProviderService
{
    use UploadHelper;

    function findById($id)
    {
        return Provider::find($id);
    }
    function findBy($key, $value)
    {
        return Provider::where($key, $value)->get();
    }
    function create($data)
    {
        if (request()->hasFile('image')) {
            $data['image'] = $this->upload(request()->file('image'), 'provider');
        }
        $provider = Provider::create($data);
        return $provider;
    }

    function verifyOtp($data)
    {
        $provider = $this->findBy('phone', $data['phone'])[0];
        if ($provider && $provider->verify_code == $data['otp']) {
            return $this->update($provider->id, ['is_active' => 1]);
        }
        return false;
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

    function changePassword($data)
    {
        $provider = auth('provider')->user();
        $provider->update([
            'password' => Hash::make($data['new_password'])
        ]);
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