<?php

namespace Modules\Shop\Service;

use Illuminate\Support\Facades\File;
use Modules\Shop\App\Models\Shop;
use Modules\Common\Helpers\UploadHelper;

class ShopService
{
    use UploadHelper;

    function findAll($data)
    {
        $shops = Shop::all();
        return getCaseCollection($shops, $data);
    }
    function findById($id)
    {
        return Shop::find($id);
    }
    function findBy($key, $value)
    {
        return Shop::where($key, $value)->get();
    }
    public function create($data)
    {
        if (request()->hasFile('image')) {
            $data['image'] = $this->upload(request()->file('image'), 'shop');
        }
        $shop = Shop::create($data);
        return $shop;
    }

    function update($shop, $data)
    {
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/Shop/' . $this->getImageName('shop', $shop->image)));
            $data['image'] = $this->upload(request()->file('image'), 'shop');
        }
        $shop->update($data);
        return $shop;
    }

    function delete($shop)
    {
        File::delete(public_path('uploads/Shop/' . $this->getImageName('shop', $shop->image)));
        $shop->delete();
    }

    function activate($shop)
    {
        $shop->is_active = !$shop->is_active;
        $shop->save();
    }
}