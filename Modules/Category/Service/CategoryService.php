<?php

namespace Modules\Category\Service;

use Illuminate\Support\Facades\File;
use Modules\Category\App\Models\Category;
use Modules\Category\App\Models\SubCategory;
use Modules\Common\Helpers\UploadHelper;

class CategoryService
{
    use UploadHelper;

    function findAll($data = [], $relations = [])
    {
        $categories = Category::query()->with($relations)->latest();
        return getCaseCollection($categories, $data);
    }

    function findSubCategories($category, $data = [], $relations = [])
    {
        $subCategories = SubCategory::query()->where('category_id', $category->id)->with($relations)->latest();
        return getCaseCollection($subCategories, $data);
    }

    function findById($id)
    {
        $category = Category::findOrFail($id);
        return $category;
    }

    function findBy($key, $value, $relations = [])
    {
        return Category::where($key, $value)->with($relations)->get();
    }

    public function create($data)
    {
        if (request()->hasFile('image')) {
            $data['image'] = $this->upload(request()->file('image'), 'category');
        }
        $category = Category::create($data);
        return $category;
    }

    function update($category, $data)
    {
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/category/' . $this->getImageName('category', $category->image)));
            $data['image'] = $this->upload(request()->file('image'), 'category');
        }
        $category->update($data);
        return $category->fresh();
    }

    function delete($category)
    {
        if ($category->image) {
            File::delete(public_path('uploads/category/' . $this->getImageName('category', $category->image)));
        }
        $category->delete();
    }

    public function toggleActivate($category)
    {
        $category->update(['is_active' => !$category->is_active]);
        return $category->fresh();
    }
}