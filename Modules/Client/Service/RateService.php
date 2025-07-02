<?php

namespace Modules\Client\Service;

use Modules\User\App\Models\User;
use Modules\Notification\Service\NotificationService;

class RateService
{
    public function create($data, $clientContact)
    {
        $updatedClientContact = $clientContact->update($data);
        (new NotificationService())->sendNotification('تقييم جديد', 'يوجد تقييم جديد من قبل عميل', $clientContact->contactable_id, User::class);
        return $updatedClientContact;
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
