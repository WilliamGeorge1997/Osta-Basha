<?php

namespace Modules\Client\Service;

class RateService
{
    public function create($data, $clientContact)
    {
        $clientContact = $clientContact->update($data);
        return $clientContact;
    }

    public function update($clientContact, $data)
    {
        $clientContact->update($data);
        return $clientContact;
    }

    public function delete($clientContact)
    {
        $clientContact->update(['rate' => null, 'comment' => null]);
        return $clientContact;
    }

}