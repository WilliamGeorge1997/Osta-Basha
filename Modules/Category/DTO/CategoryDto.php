<?php


namespace Modules\Category\DTO;

class CategoryDto
{
    public $title;
    public $image;

    public function __construct($request)
    {
        if ($request->get('title'))
            $this->title = $request->get('title');
        if ($request->get('image'))
            $this->image = $request->get('image');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title == null)
            unset($data['title']);
        if ($this->image == null)
            unset($data['image']);
        return $data;
    }
}

