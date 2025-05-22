<?php

namespace Modules\Client\Service;

use Modules\User\App\Models\User;
use Modules\Provider\App\Models\Provider;
use Modules\ShopOwner\App\Models\ShopOwner;
use Modules\Client\App\Models\ClientContact;


class ClientService
{
    function findAll($data = [], $relations = [])
    {
        $clients = User::query()
            ->where('type', 'client')
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
            ->latest();
        return getCaseCollection($clients, $data);
    }
    public function clientContact($data)
    {
        $contactable = User::findOrFail($data['contactable_id']);
        $data['contactable_type'] = $contactable->type === 'service_provider'
            ? Provider::class
            : ShopOwner::class;

            $data['client_id'] = auth('user')->id();
            $contact = ClientContact::create($data);

        if (in_array($contactable->type, ['service_provider', 'shop_owner'])) {
            $profileRelation = $contactable->type === 'service_provider' ? 'providerProfile' : 'shopOwnerProfile';
            $contactable->load($profileRelation);
            if ($contactable->{$profileRelation}->status === 'free_trial') {
                $this->handleFreeTrialContact($contactable, $data['contactable_id'], $profileRelation);
            }
        }
        return $contact;
    }

    private function handleFreeTrialContact($contactable, $contactableId, $profileRelation)
    {
        $contactCount = ClientContact::where('contactable_id', $contactableId)->count();
        $freeTrialContactCount = \Modules\Common\App\Models\Setting::where('key', 'free_trial_contacts_count')->first();
        if ($contactCount >= $freeTrialContactCount->value) {
            $contactable->{$profileRelation}->is_active = 0;
            $contactable->{$profileRelation}->save();
        }
    }

}