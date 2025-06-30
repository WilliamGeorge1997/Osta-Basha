<?php

namespace Modules\User\App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Modules\User\App\Models\User;
use Modules\Notification\App\Models\Notification;

class NotificationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:user');
    }

    public function index()
    {
        return returnMessage(true, 'User Notifications', Auth::user()->notifications()->select('id', 'title', 'description', 'image', 'created_at', 'read_at')->orderByDesc('id')->paginate(5));
    }

    public function allow_notification()
    {
        $user = Auth::user();
        $user->allow_notification = !$user->allow_notification;
        $user->save();
        return returnMessage(true, 'User Updated Successfully');
    }

    public function readNotification(Request $request)
    {
        Notification::whereIn('id', $request['notifications_ids'])->update(['read_at' => Carbon::now()]);
        return returnMessage(true, 'Notification read successfully');
    }

    public function unReadNotificationsCount()
    {
        $unReadCount = Notification::whereNull('read_at')->whereHasMorph('notifiable', [User::class], function ($query) {
            $query->where('notifiable_id', Auth::id());
        })->count();
        return returnMessage(true, 'Unread Notifications Count', $unReadCount);
    }
}
