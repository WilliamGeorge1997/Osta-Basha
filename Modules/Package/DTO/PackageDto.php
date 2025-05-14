<?php


namespace Modules\Package\DTO;

class PackageDto
{
    public $title;
    public $description;
    public $price;
    public $duration;

    public function __construct($request)
    {
        if ($request->get('title'))
            $this->title = $request->get('title');
        if ($request->get('description'))
            $this->description = $request->get('description');
        if ($request->get('price'))
            $this->price = $request->get('price');
        if ($request->get('duration'))
            $this->duration = $request->get('duration');
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
        if ($this->duration == null)
            unset($data['duration']);
        return $data;
    }
}

