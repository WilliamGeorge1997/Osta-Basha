<?php

namespace Modules\Category\Service;

use Illuminate\Support\Facades\File;
use Modules\Common\Helpers\UploadHelper;
use Modules\Category\App\Models\SubCategory;

class SubCategoryService
{
    use UploadHelper;

    function findAll($data = [], $relations = [])
    {
        $categories = SubCategory::query()->with($relations)->latest();
        return getCaseCollection($categories, $data);
    }

    function findById($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        return $subCategory;
    }

    function findBy($key, $value, $relations = [])
    {
        return SubCategory::where($key, $value)->with($relations)->get();
    }

    public function create($data)
    {
        if (request()->hasFile('image')) {
            $data['image'] = $this->upload(request()->file('image'), 'sub_category');
        }
        $subCategory = SubCategory::create($data);
        return $subCategory;
    }

    public function active($category, $data = [], $relations = [])
    {
        $subCategories = SubCategory::query()->active()->where('category_id', $category->id)->with($relations)->latest();
        return getCaseCollection($subCategories, $data);
    }
    function update($subCategory, $data)
    {
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/sub_category/' . $this->getImageName('sub_category', $subCategory->image)));
            $data['image'] = $this->upload(request()->file('image'), 'sub_category');
        }
        $subCategory->update($data);
        return $subCategory->fresh();
    }

    function delete($subCategory)
    {
        if ($subCategory->image) {
            File::delete(public_path('uploads/sub_category/' . $this->getImageName('sub_category', $subCategory->image)));
        }
        $subCategory->delete();
    }

    public function toggleActivate($subCategory)
    {
        $subCategory->update(['is_active' => !$subCategory->is_active]);
        return $subCategory->fresh();
    }
}