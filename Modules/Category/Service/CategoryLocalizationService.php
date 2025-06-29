<?php

namespace Modules\Category\Service;

use Modules\Category\App\Models\CategoryLocalization;

class CategoryLocalizationService
{
    function findAll($data = [], $relations = [])
    {
        $localizations = CategoryLocalization::query()->with($relations)->latest();
        return getCaseCollection($localizations, $data);
    }

    function findById($id)
    {
        $localization = CategoryLocalization::findOrFail($id);
        return $localization;
    }

    function findByCategory($categoryId, $data = [], $relations = [])
    {
        $localizations = CategoryLocalization::query()
            ->where('category_id', $categoryId)
            ->with($relations)
            ->latest();
        return getCaseCollection($localizations, $data);
    }

    function findBy($key, $value, $relations = [])
    {
        return CategoryLocalization::where($key, $value)->with($relations)->get();
    }

    function active($data = [], $relations = [])
    {
        $localizations = CategoryLocalization::query()->active()->with($relations)->latest();
        return getCaseCollection($localizations, $data);
    }

    public function create($data)
    {
        $localizations = [];
        foreach ($data['country_ids'] as $countryId) {
            $data['country_id'] = $countryId;
            $localizations[] = CategoryLocalization::create($data);
        }
        return $localizations;
    }

    function update($localization, $data)
    {
        // If sub_title is provided, use it as the title
        if (isset($data['sub_title'])) {
            $data['title'] = $data['sub_title'];
            unset($data['sub_title']);
        }

        $localization->update($data);
        return $localization->fresh();
    }

    function delete($localization)
    {
        $localization->delete();
    }

    function deleteByCategory($categoryId)
    {
        CategoryLocalization::where('category_id', $categoryId)->delete();
    }

    function deleteByCategoryAndCountries($categoryId, $countryIds)
    {
        CategoryLocalization::where('category_id', $categoryId)
            ->whereIn('country_id', $countryIds)
            ->delete();
    }

    public function toggleActivate($localization)
    {
        $localization->update(['is_active' => !$localization->is_active]);
        return $localization->fresh();
    }
}