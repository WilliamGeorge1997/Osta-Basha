<?php

namespace Modules\Country\DTO;

class CountryDto
{
    public $title;
    public $currency;

    public function __construct($request)
    {
        if ($request->get('title'))
            $this->title = $request->get('title');
        if ($request->get('currency'))
            $this->currency = $request->get('currency');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title == null)
            unset($data['title']);
        if ($this->currency == null)
            unset($data['currency']);
        return $data;
    }
}