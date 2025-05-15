<?php


namespace Modules\ShopOwner\DTO;

use Carbon\Carbon;
use Modules\Common\App\Models\Setting;


class ShopOwnerDto
{
    public $user_id;
    public $shop_name;
    public $products_description;
    public $address;
    public $start_date;
    public $end_date;
    public $is_active;
    public $sub_category_id;


    public function __construct($request, $user_id)
    {
        $this->user_id = $user_id;
        if ($request->get('shop_name'))
            $this->shop_name = $request->get('shop_name');
        if ($request->get('products_description'))
            $this->products_description = $request->get('products_description');
        if ($request->get('address'))
            $this->address = $request->get('address');
        if ($request->get('sub_category_id'))
            $this->sub_category_id = $request->get('sub_category_id');
        $this->start_date = Carbon::now()->toDateString();
        $freeTrialMonths = $this->getFreeTrialMonths();
        $this->end_date = Carbon::now()->addMonths($freeTrialMonths)->toDateString();
        $this->is_active = 1;
    }

    private function getFreeTrialMonths()
    {
        $setting = Setting::where('key', 'free_trial_months')->first();
        return $setting ? (int) $setting->value : 3;
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

