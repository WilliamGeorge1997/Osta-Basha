<?php


namespace Modules\Category\DTO;

class CategoryDto
{
    public $title;
    public $description;
    public $image;
    public $sub_title;
    public $country_ids;

    public function __construct($request)
    {
        if ($request->get('title'))
            $this->title = $request->get('title');
        if ($request->get('image'))
            $this->image = $request->get('image');
        if ($request->get('description'))
            $this->description = $request->get('description');
        if ($request->get('sub_title'))
            $this->sub_title = $request->get('sub_title');
        if ($request->get('country_ids'))
            $this->country_ids = $request->get('country_ids');
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
        if ($this->sub_title == null)
            unset($data['sub_title']);
        if ($this->country_ids == null)
            unset($data['country_ids']);
        return $data;
    }
}

