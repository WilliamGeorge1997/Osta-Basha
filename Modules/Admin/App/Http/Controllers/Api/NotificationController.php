<?php

namespace Modules\Admin\App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Modules\Admin\App\Models\Admin;
use Modules\Notification\App\Models\Notification;

class NotificationController extends Controller
{

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
}
