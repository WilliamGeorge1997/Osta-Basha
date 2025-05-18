<?php


namespace Modules\ShopOwner\DTO;



class ShopOwnerDto
{
    public $shop_name;
    public $products_description;
    public $address;
    public $sub_category_id;


    public function __construct($request)
    {
        if ($request->get('shop_name'))
            $this->shop_name = $request->get('shop_name');
        if ($request->get('products_description'))
            $this->products_description = $request->get('products_description');
        if ($request->get('address'))
            $this->address = $request->get('address');
        if ($request->get('sub_category_id'))
            $this->sub_category_id = $request->get('sub_category_id');
    }


    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->shop_name == null)
            unset($data['shop_name']);
        if ($this->products_description == null)
            unset($data['products_description']);
        if ($this->address == null)
            unset($data['address']);

        return $data;
    }
}

