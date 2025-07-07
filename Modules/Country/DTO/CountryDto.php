<?php

namespace Modules\Country\DTO;

class CountryDto
{
    public $title_ar;
    public $title_en;
    public $currency_ar;
    public $currency_en;
    public $country_code;
    public function __construct($request)
    {
        if ($request->get('title_ar'))
            $this->title_ar = $request->get('title_ar');
        if ($request->get('title_en'))
            $this->title_en = $request->get('title_en');
        if ($request->get('currency_ar'))
            $this->currency_ar = $request->get('currency_ar');
        if ($request->get('currency_en'))
            $this->currency_en = $request->get('currency_en');
        if ($request->get('country_code'))
            $this->country_code = $request->get('country_code');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title_ar == null)
            unset($data['title_ar']);
        if ($this->title_en == null)
            unset($data['title_en']);
        if ($this->currency_ar == null)
            unset($data['currency_ar']);
        if ($this->currency_en == null)
            unset($data['currency_en']);
        if ($this->country_code == null)
            unset($data['country_code']);
        return $data;
    }
}