<?php

namespace Modules\ShopOwner\Service;

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Package\App\Models\Package;


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
                $country = DB::table('countries')
                    ->where(function ($q) use ($data) {
                        $q->where('title_en', $data['country'])
                            ->orWhere('title_ar', $data['country']);
                    })
                    ->first(['title_en', 'title_ar']);

                if ($country) {
                    $query->where(function ($q) use ($country) {
                        $q->where('country', $country->title_en)
                            ->orWhere('country', $country->title_ar);
                    });
                }
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
            ->with('shopOwnerContacts.client')
            ->withCount([
                'shopOwnerContacts as rates_count' => function ($q) {
                    $q->whereNotNull('rate');
                }
            ])
            ->withCount([
                'shopOwnerContacts as comments_count' => function ($q) {
                    $q->whereNotNull('comment');
                }
            ])
            ->withAvg([
                'shopOwnerContacts as rates_avg' => function ($q) {
                    $q->whereNotNull('rate');
                }
            ], 'rate')
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->latest();
        return getCaseCollection($shopOwners, $data);
    }

    function updateSubscription($user, $data)
    {
        $package = Package::find($data['package_id']);
        $startDate = \Carbon\Carbon::parse($data['start_date']);
        $endDate = $startDate->copy()->addDays($package->duration);
        $data['end_date'] = $endDate->toDateString();
        $data['status'] = 'subscribed';
        $data['is_active'] = 1;
        $user->shopOwnerProfile->update($data);
        return $user;
    }

}