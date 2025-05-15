<?php

namespace Modules\ShopOwner\Service;

use Modules\User\App\Models\User;


class ShopOwnerService
{
    function findAll($data = [], $relations = [])
    {
        $clients = User::query()
            ->where('type', 'shop_owner')
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
            ->with($relations)
            ->latest();
        return getCaseCollection($clients, $data);
    }

    function active($data = [], $relations = [])
    {
        $shopOwners = User::query()
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
            ->where('type', 'shop_owner')
            ->whereHas('shopOwnerProfile', function ($query) use ($data) {
                $query
                    ->when($data['sub_category_id'] ?? null, function ($q) use ($data) {
                        $q->where('sub_category_id', $data['sub_category_id']);
                    })
                    ->active()
                    ->withinActiveSubscriptionPeriod();

            })
            ->with($relations)
            ->when(auth('user')->check(), function ($query) {
                $query->withCount([
                    'shopOwnerContacts as is_contacted' => function ($q) {
                        $q->where('client_id', auth('user')->id());
                    }
                ]);
            });
        return getCaseCollection($shopOwners, $data);
    }

}