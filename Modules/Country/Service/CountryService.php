<?php

namespace Modules\Country\Service;

use Modules\Common\App\Models\Currency;
use Modules\Country\App\Models\Country;


class CountryService
{

    function findAll($data = [], $relations = [])
    {
        $countries = Country::query()
            ->when($data['title'] ?? null, function ($query) use ($data) {
                $query->where('title', 'like', '%' . $data['title'] . '%');
            })
            ->with($relations)
            ->latest();
        return getCaseCollection($countries, $data);
    }

    function findById($id)
    {
        $country = Country::findOrFail($id);
        return $country;
    }

    function findBy($key, $value)
    {
        return Country::where($key, $value)->get();
    }

    function active($data = [], $relations = [])
    {
        $countries = Country::query()
            ->when($data['title'] ?? null, function ($query) use ($data) {
                $query->where('title', 'like', '%' . $data['title'] . '%');
            })
            ->active()
            ->with($relations)
            ->latest();
        return getCaseCollection($countries, $data);
    }
    public function create($data, $currencyData)
    {
        $country = Country::create($data);
        $currencyData['country_id'] = $country->id;
        Currency::create($currencyData);
        return $country;
    }

    function update($country, $data, $currencyData)
    {
        $country->update($data);
        $country->currency->update($currencyData);
        return $country->fresh();
    }

    function delete($country)
    {
        $country->delete();
    }

    public function toggleActivate($country)
    {
        $country->update(['is_active' => !$country->is_active]);
        return $country->fresh();
    }
}