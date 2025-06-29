<?php

namespace Modules\Category\DTO;

class CategoryLocalizationDto
{
    public $title;
    public $category_id;
    public $country_ids = [];

    public function __construct($request)
    {
        if ($request->get('title'))
            $this->title = $request->get('title');
        if ($request->get('category_id'))
            $this->category_id = $request->get('category_id');
        if ($request->get('country_ids'))
            $this->country_ids = $request->get('country_ids');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title == null)
            unset($data['title']);
        if ($this->category_id == null)
            unset($data['category_id']);
        if (empty($this->country_ids))
            unset($data['country_ids']);
        return $data;
    }
}