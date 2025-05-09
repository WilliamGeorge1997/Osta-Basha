<?php


namespace Modules\Service\DTO;

class ServiceDto
{
    public $title;
    public $description;
    public $price;
    public $image;
    public $user_id;

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
        return $data;
    }
}

