<?php

namespace Modules\Country\Service;

use Modules\Common\Helpers\UploadHelper;
use Modules\Country\App\Models\Country;
use Illuminate\Support\Facades\File;


class CountryService
{
    use UploadHelper;
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
    public function create($data)
    {
        if (request()->hasFile('image')) {
            $data['image'] = $this->upload(request()->file('image'), 'country');
        }
        $country = Country::create($data);
        return $country;
    }

    function update($country, $data)
    {
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/country/' . $this->getImageName('country', $country->image)));
            $data['image'] = $this->upload(request()->file('image'), 'country');
        }
        $country->update($data);
        return $country->fresh();
    }

    function delete($country)
    {
        File::delete(public_path('uploads/country/' . $this->getImageName('country', $country->image)));
        $country->delete();
    }

    public function toggleActivate($country)
    {
        $country->update(['is_active' => !$country->is_active]);
        return $country->fresh();
    }
}