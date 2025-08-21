<?php

namespace Modules\Admin\App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Modules\Common\Helpers\UploadHelper;
use Modules\Admin\App\Jobs\SendNotificationJob;
use Modules\Notification\App\Models\Notification;
use Modules\Admin\App\Http\Requests\CreateNotificationRequest;

class NotificationController extends Controller
{
    use UploadHelper;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        return returnMessage(true, 'Admin Notifications', Auth::user()->notifications()->select('id', 'title', 'description', 'image', 'created_at', 'read_at')->orderByDesc('id')->paginate(5));
    }

    public function allow_notification()
    {
        $admin = Auth::user();
        $admin->allow_notification = !$admin->allow_notification;
        $admin->save();
        return returnMessage(true, 'Admin Updated Successfully');
    }

    public function readNotification(Request $request)
    {
        Notification::whereIn('id', $request['notifications_ids'])->update(['read_at' => Carbon::now()]);
        return returnMessage(true, 'Notification read successfully');
    }

    public function unReadNotificationsCount()
    {
        $unReadCount = Notification::whereNull('read_at')->whereHasMorph('notifiable', [Admin::class], function ($query) {
            $query->where('notifiable_id', Auth::id());
        })->count();
        return returnMessage(true, 'Unread Notifications Count', $unReadCount);
    }

    public function createNotification(CreateNotificationRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $image = null;

            if ($request->hasFile('image')) {
                $image = $this->upload($request->file('image'), 'notification');
            }
            SendNotificationJob::dispatch(
                $data['title'],
                $data['description'],
                $image,
                $data['target_type'] ?? null,
                $data['target_ids'] ?? []
            )->onConnection('database');

            DB::commit();

            $targetInfo = isset($data['target_type'])
                ? "user group: {$data['target_type']}"
                : count($data['target_ids'] ?? []) . " specific users";

            return returnMessage(true, "Notification sent successfully to {$targetInfo}. Users will receive it shortly.");

        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

}
