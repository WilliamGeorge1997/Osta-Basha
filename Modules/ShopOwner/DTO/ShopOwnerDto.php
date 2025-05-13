<?php


namespace Modules\ShopOwner\DTO;


class ShopOwnerDto
{
    public $user_id;
    public $card_number;
    public $card_image;
    public $address;
    public $experience_years;
    public $experience_description;
    public $min_price;
    public $max_price;

    public function __construct($request, $user_id)
    {
        $this->user_id = $user_id;
        if ($request->get('card_number'))
            $this->card_number = $request->get('card_number');
        if ($request->get('card_image'))
            $this->card_image = $request->get('card_image');
        if ($request->get('address'))
            $this->address = $request->get('address');
        if ($request->get('experience_years'))
            $this->experience_years = $request->get('experience_years');
        if ($request->get('experience_description'))
            $this->experience_description = $request->get('experience_description');
        if ($request->get('min_price'))
            $this->min_price = $request->get('min_price');
        if ($request->get('max_price'))
            $this->max_price = $request->get('max_price');
        if ($request->get('sub_category_id'))
            $this->sub_category_id = $request->get('sub_category_id');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->card_number == null)
            unset($data['card_number']);
        if ($this->card_image == null)
            unset($data['card_image']);
        if ($this->address == null)
            unset($data['address']);
        if ($this->experience_years == null)
            unset($data['experience_years']);
        if ($this->experience_description == null)
            unset($data['experience_description']);
        if ($this->min_price == null)
            unset($data['min_price']);
        if ($this->max_price == null)
            unset($data['max_price']);
        return $data;
    }
}

