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
            ->when($data['city'] ?? null, function ($query) use ($data) {
                $query->where('city', $data['city']);
            })
            ->when($data['country'] ?? null, function ($query) use ($data) {
                $query->where('country', $data['country']);
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
            })
            ->with([
                'rates' => function ($q) {
                    $q->where('rateable_type', \Modules\ShopOwner\App\Models\ShopOwner::class);
                }
            ])
            ->withCount('rates as rates_count')
            ->withAvg('rates as rates_avg', 'rate')
            ->with([
                'comments' => function ($q) {
                    $q->where('commentable_type', \Modules\ShopOwner\App\Models\ShopOwner::class);
                }
            ])
            ->withCount('comments as comments_count')
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->latest();
        return getCaseCollection($shopOwners, $data);
    }

}