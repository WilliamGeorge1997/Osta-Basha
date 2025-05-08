<?php


namespace Modules\Category\DTO;


class SubCategoryDto
{
    public $title;
    public $image;
    public $category_id;
    public function __construct($request)
    {
        if ($request->get('title'))
            $this->title = $request->get('title');
        if ($request->get('image'))
            $this->image = $request->get('image');
        if ($request->get('category_id'))
            $this->category_id = $request->get('category_id');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title == null)
            unset($data['title']);
        if ($this->image == null)
            unset($data['image']);
        if ($this->category_id == null)
            unset($data['category_id']);
        return $data;
    }
}

