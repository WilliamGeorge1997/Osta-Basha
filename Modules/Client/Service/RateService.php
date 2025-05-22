<?php

namespace Modules\Client\Service;

use Modules\Client\App\Models\Rate;



class RateService
{
    public function create($data)
    {
        $rate = Rate::create($data);
        return $rate;
    }

    public function update($rate, $data)
    {
        $rate->update($data);
        return $rate;
    }

    public function delete($rate)
    {
        $rate->delete();
        return $rate;
    }

}