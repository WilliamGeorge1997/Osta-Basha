<?php


namespace Modules\Service\DTO;

class ServiceAdminDto
{
    public $title;
    public $description;
    public $price;
    public $image;
    public $user_id;
    public $sub_category_id;
    public $start_date;
    public $end_date;
    public $is_active;
    public function __construct($request)
    {
        if ($request->get('title'))
            $this->title = $request->get('title');
        if ($request->get('description'))
            $this->description = $request->get('description');
        if ($request->get('price'))
            $this->price = $request->get('price');
        if ($request->get('image'))
            $this->image = $request->get('image');
        if ($request->get('user_id'))
            $this->user_id = $request->get('user_id');
        if ($request->get('sub_category_id'))
            $this->sub_category_id = $request->get('sub_category_id');
        if ($request->get('start_date'))
            $this->start_date = $request->get('start_date');
        if ($request->get('end_date'))
            $this->end_date = $request->get('end_date');
        $this->is_active = isset($request['is_active']) ? 1 : 0;
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title == null)
            unset($data['title']);
        if ($this->description == null)
            unset($data['description']);
        if ($this->price == null)
            unset($data['price']);
        if ($this->image == null)
            unset($data['image']);
        if ($this->user_id == null)
            unset($data['user_id']);
        if ($this->sub_category_id == null)
            unset($data['sub_category_id']);
        if ($this->start_date == null)
            unset($data['start_date']);
        if ($this->end_date == null)
            unset($data['end_date']);
        
        return $data;
    }
}

