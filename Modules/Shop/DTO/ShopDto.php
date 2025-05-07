<?php


namespace Modules\Shop\DTO;

use Illuminate\Support\Facades\Hash;

class ShopDto
{
    public $title;
    public $description;
    public $image;
    public $user_id;

    public function __construct($request)
    {
        if ($request->get('title'))
            $this->title = $request->get('title');
        if ($request->get('description'))
            $this->description = $request->get('description');
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
        if ($this->image == null)
            unset($data['image']);
        if ($this->user_id == null)
            unset($data['user_id']);
        return $data;
    }
}

