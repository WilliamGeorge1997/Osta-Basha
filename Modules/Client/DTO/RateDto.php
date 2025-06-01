<?php


namespace Modules\Client\DTO;

use Modules\User\App\Models\User;
use Modules\Provider\App\Models\Provider;
use Modules\ShopOwner\App\Models\ShopOwner;

class RateDto
{
    public $client_contact_id;
    public $rate;
    public $comment;

    public function __construct($request)
    {
        if ($request->get('client_contact_id'))
            $this->client_contact_id = $request->get('client_contact_id');
        if ($request->get('rate'))
            $this->rate = $request->get('rate');
        if ($request->get('comment'))
            $this->comment = $request->get('comment');
    }
    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->client_contact_id == null)
            unset($data['client_contact_id']);
        if ($this->rate == null)
            unset($data['rate']);
        if ($this->comment == null)
            unset($data['comment']);
        return $data;
    }
}

