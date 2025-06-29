<?php

namespace Modules\Category\Service;

use Modules\Category\App\Models\SubCategoryLocalization;

class SubCategoryLocalizationService
{
    function findAll($data = [], $relations = [])
    {
        $localizations = SubCategoryLocalization::query()->with($relations)->latest();
        return getCaseCollection($localizations, $data);
    }

    function findById($id)
    {
        $localization = SubCategoryLocalization::findOrFail($id);
        return $localization;
    }

    function findBySubCategory($subCategoryId, $data = [], $relations = [])
    {
        $localizations = SubCategoryLocalization::query()
            ->where('sub_category_id', $subCategoryId)
            ->with($relations)
            ->latest();
        return getCaseCollection($localizations, $data);
    }

    function findBy($key, $value, $relations = [])
    {
        return SubCategoryLocalization::where($key, $value)->with($relations)->get();
    }

    function active($data = [], $relations = [])
    {
        $localizations = SubCategoryLocalization::query()->active()->with($relations)->latest();
        return getCaseCollection($localizations, $data);
    }

    public function create($data)
    {
        $localizations = [];
        foreach ($data['country_ids'] as $countryId) {
            $data['country_id'] = $countryId;
            $localizations[] = SubCategoryLocalization::create($data);
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

    function deleteBySubCategory($subCategoryId)
    {
        SubCategoryLocalization::where('sub_category_id', $subCategoryId)->delete();
    }

    function deleteBySubCategoryAndCountries($subCategoryId, $countryIds)
    {
        SubCategoryLocalization::where('sub_category_id', $subCategoryId)
            ->whereIn('country_id', $countryIds)
            ->delete();
    }

    public function toggleActivate($localization)
    {
        $localization->update(['is_active' => !$localization->is_active]);
        return $localization->fresh();
    }
}