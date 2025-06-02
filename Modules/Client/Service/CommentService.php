<?php

namespace Modules\Client\Service;




class CommentService
{
    public function delete($clientContact)
    {
        $clientContact->update(['comment' => null]);
        return $clientContact;
    }
}