<?php

namespace Modules\Client\Service;

use Modules\Client\App\Models\Rate;
use Modules\Client\App\Models\ClientContact;



class RateService
{
    public function findByContactId($contactId)
    {
        return ClientContact::findOrFail($contactId);
    }
    public function create($data, $contactId)
    {
        $contact = $this->findByContactId($contactId);
        $contact->update($data);
        return $contact;
    }

    public function update($clientContact, $data)
    {
        $clientContact->update($data);
        return $clientContact;
    }

    public function delete($clientContact)
    {
        $clientContact->update([
            'rate' => null,
            'comment' => null,
            'updated_at' => now()
        ]);
        return $clientContact;
    }

}