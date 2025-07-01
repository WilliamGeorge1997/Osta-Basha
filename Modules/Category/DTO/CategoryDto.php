<?php


namespace Modules\Category\DTO;

class CategoryDto
{
    public $title_ar;
    public $title_en;
    public $description_ar;
    public $description_en;
    public $image;
    public $sub_title_ar;
    public $sub_title_en;
    public $country_ids;
    public $localizations;
    public function __construct($request)
    {
        if ($request->get('title_ar'))
            $this->title_ar = $request->get('title_ar');
        if ($request->get('title_en'))
            $this->title_en = $request->get('title_en');
        if ($request->get('image'))
            $this->image = $request->get('image');
        if ($request->get('description_ar'))
            $this->description_ar = $request->get('description_ar');
        if ($request->get('description_en'))
            $this->description_en = $request->get('description_en');
        if ($request->get('sub_title_ar'))
            $this->sub_title_ar = $request->get('sub_title_ar');
        if ($request->get('sub_title_en'))
            $this->sub_title_en = $request->get('sub_title_en');
        if ($request->get('country_ids'))
            $this->country_ids = $request->get('country_ids');
        if ($request->get('localizations'))
            $this->localizations = $request->get('localizations');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title_ar == null)
            unset($data['title_ar']);
        if ($this->title_en == null)
            unset($data['title_en']);
        if ($this->image == null)
            unset($data['image']);
        if ($this->description_ar == null)
            unset($data['description_ar']);
        if ($this->description_en == null)
            unset($data['description_en']);
        if ($this->sub_title_ar == null)
            unset($data['sub_title_ar']);
        if ($this->sub_title_en == null)
            unset($data['sub_title_en']);
        if ($this->country_ids == null)
            unset($data['country_ids']);
        if ($this->localizations == null)
            unset($data['localizations']);
        return $data;
    }
}

