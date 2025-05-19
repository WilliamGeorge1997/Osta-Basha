<?php

namespace Modules\Common\Service;

use Modules\Common\App\Models\Page;
use Modules\Common\Helpers\UploadHelper;

class PageService
{
    use UploadHelper;

    function findAll($data = [], $relations = [])
    {
        $pages = Page::query()->with($relations)->latest();
        return getCaseCollection($pages, $data);
    }

    function findByPage($data)
    {
        $page = Page::where('page', $data['page'])->first();
        return $page;
    }

    function findById($id)
    {
        $page = Page::findOrFail($id);
        return $page;
    }

    function findBy($key, $value, $relations = [])
    {
        return Page::where($key, $value)->with($relations)->get();
    }

    function active($data = [], $relations = [])
    {
        $pages = Page::query()->active()->with($relations)->latest();
        return getCaseCollection($pages, $data);
    }
    public function create($data)
    {
        $page = Page::create($data);
        return $page;
    }

    function update($page, $data)
    {
        $page->update($data);
        return $page->fresh();
    }

    function delete($page)
    {
        $page->delete();
    }

}