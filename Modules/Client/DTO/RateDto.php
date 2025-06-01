<?php


namespace Modules\Client\DTO;

use Modules\User\App\Models\User;
use Modules\Provider\App\Models\Provider;
use Modules\ShopOwner\App\Models\ShopOwner;

class RateDto
{
    public $rate;
    public $comment;

    public function __construct($request)
    {
        if ($request->get('rate'))
            $this->rate = $request->get('rate');
        if ($request->get('comment'))
            $this->comment = $request->get('comment');
    }
    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->rate == null)
            unset($data['rate']);
        if ($this->comment == null)
            unset($data['comment']);
        return $data;
    }
}

