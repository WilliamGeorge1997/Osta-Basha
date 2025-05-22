<?php


namespace Modules\Client\DTO;

use Modules\User\App\Models\User;
use Modules\Provider\App\Models\Provider;
use Modules\ShopOwner\App\Models\ShopOwner;

class RateDto
{
    public $client_id;
    public $rate;
    public $rateable_id;
    public $rateable_type;

    public function __construct($request)
    {
        $this->client_id = auth()->id();
        if ($request->get('rate'))
            $this->rate = $request->get('rate');
        if ($request->get('rateable_id'))
            $this->rateable_id = $request->get('rateable_id');
        $this->rateable_type = $this->getType();
    }

    private function getType()
    {
        $user = User::findOrFail($this->rateable_id);
        return $user->type == User::TYPE_SERVICE_PROVIDER ? Provider::class : ShopOwner::class;
    }
    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->client_id == null)
            unset($data['client_id']);
        if ($this->rate == null)
            unset($data['rate']);
        if ($this->rateable_id == null)
            unset($data['rateable_id']);
        if ($this->rateable_type == null)
            unset($data['rateable_type']);
        return $data;
    }
}

