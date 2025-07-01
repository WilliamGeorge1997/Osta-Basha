<?php

namespace Modules\Category\DTO;

class CategoryLocalizationDto
{
    public $title_ar;
    public $title_en;
    public $category_id;
    public $country_ids = [];

    public function __construct($request)
    {
        if ($request->get('title_ar'))
            $this->title_ar = $request->get('title_ar');
        if ($request->get('title_en'))
            $this->title_en = $request->get('title_en');
        if ($request->get('category_id'))
            $this->category_id = $request->get('category_id');
        if ($request->get('country_ids'))
            $this->country_ids = $request->get('country_ids');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title_ar == null)
            unset($data['title_ar']);
        if ($this->title_en == null)
            unset($data['title_en']);
        if ($this->category_id == null)
            unset($data['category_id']);
        if (empty($this->country_ids))
            unset($data['country_ids']);
        return $data;
    }
}