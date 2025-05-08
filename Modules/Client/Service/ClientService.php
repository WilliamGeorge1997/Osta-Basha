<?php

namespace Modules\Client\Service;

use Modules\Client\App\Models\ClientProviderContact;


class ClientService
{
    public function contactProvider($data)
    {
        $data['client_id'] = auth()->id();
        return ClientProviderContact::create($data);
    }
}