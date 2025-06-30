<?php


namespace Modules\Notification\Service;

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Common\Helper\FCMService;
use Modules\User\Service\UserService;
use Modules\Admin\Service\AdminService;
use Modules\Common\Helpers\UploadHelper;
use Modules\Notification\App\Models\Notification;

class NotificationService
{

    use UploadHelper;

    function findAll()
    {
        return Notification::all();
    }


    function findById($id)
    {
        return Notification::findOrFail($id);
    }

    function findBy($key, $value)
    {
        return Notification::with('notifiable')->where($key, $value)->get();
    }

    function NotificationsInAdminPanel()
    {
        return Notification::groupBy('group_by')->whereNull('order_id')->select('id', 'group_by', 'created_at', 'title', DB::raw('count(*) as total'), DB::raw("count(DISTINCT(read_at)) as readCount"))->get();
    }

    function save($data, $model)
    {
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $imageName = $this->upload($image, 'notification');
            $data['image'] = $imageName;
        }

        Notification::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'image' => @$data['image'],
            'notifiable_id' => @$data['user_id'],
            'notifiable_type' => $model,
            'group_by' => @$data['group_by']
        ]);
    }

    function sendNotification($title, $description, $user_id, $model)
    {
        $data = [
            'title' => $title,
            'description' => $description,
            'user_id' => $user_id,
        ];
        $this->save($data, $model);
        $fcm = new FCMService;
        $user_token = $model === User::class ?
            (new UserService())->findToken($user_id) :
            (new AdminService())->findToken($user_id);
        if ($user_token ?? null)
            $fcm->sendNotification($data, [$user_token]);
    }
}
