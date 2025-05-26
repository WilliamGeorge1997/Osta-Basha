<?php


namespace Modules\Provider\DTO;



class ProviderDto
{
    public $card_number;
    public $address;
    public $experience_years;
    public $experience_description;
    public $price;
    public $sub_category_id;

    public function __construct($request)
    {

        if ($request->get('card_number'))
            $this->card_number = $request->get('card_number');
        if ($request->get('address'))
            $this->address = $request->get('address');
        if ($request->get('experience_years'))
            $this->experience_years = $request->get('experience_years');
        if ($request->get('experience_description'))
            $this->experience_description = $request->get('experience_description');
        if ($request->get('price'))
            $this->price = $request->get('price');
        if ($request->get('sub_category_id'))
            $this->sub_category_id = $request->get('sub_category_id');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->card_number == null)
            unset($data['card_number']);
        if ($this->address == null)
            unset($data['address']);
        if ($this->experience_years == null)
            unset($data['experience_years']);
        if ($this->experience_description == null)
            unset($data['experience_description']);
        if ($this->price == null)
            unset($data['price']);
        if ($this->sub_category_id == null)
            unset($data['sub_category_id']);

        return $data;
    }
}

