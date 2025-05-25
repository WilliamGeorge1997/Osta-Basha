<?php

namespace Modules\Country\DTO;

class CountryDto
{
    public $title;


    public function __construct($request)
    {
        if ($request->get('title'))
            $this->title = $request->get('title');
        

    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title == null)
            unset($data['title']);

        return $data;
    }
}