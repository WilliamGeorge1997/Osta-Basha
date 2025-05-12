<?php


namespace Modules\Category\DTO;

class CategoryDto
{
    public $title;
    public $description;
    public $image;

    public function __construct($request)
    {
        if ($request->get('title'))
            $this->title = $request->get('title');
        if ($request->get('image'))
            $this->image = $request->get('image');
        if ($request->get('description'))
            $this->description = $request->get('description');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title == null)
            unset($data['title']);
        if ($this->image == null)
            unset($data['image']);
        if ($this->description == null)
            unset($data['description']);
        return $data;
    }
}

