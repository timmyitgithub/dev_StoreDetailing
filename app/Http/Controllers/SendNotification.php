<?php

namespace App\Http\Controllers;

//use pusher
use Pusher\Pusher;

use Illuminate\Http\Request;
use App\Notifications\TestNotification;
use App\Models\User;

class SendNotification extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function create()
    {
        return view('admin.notification');
    }

    public function store(Request $request)
    {
        $user = User::find(1); // id của user mình đã đăng kí ở trên, user này sẻ nhận được thông báo
        $data = $request->only([
            'title',
            'content',
        ]);
        $data['created_at'] = now();
        $user->notify(new TestNotification($data));
        $options = array(
            'cluster' => 'ap1',
            'encrypted' => true
        );

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger('NotificationEvent', 'send-message', $data);

        return view('admin.notification')->with('success', 'Thêm thông báo thành công!');
    }

    public function markAsRead(Request $request, $notification)
    {
        $notification = $request->user()->notifications()->findOrFail($notification);
        $notification->markAsRead();
        return redirect()->back();
    }
    public function markUnRead(Request $request, $notification)
    {
        $notification = $request->user()->notifications()->findOrFail($notification);
        $notification->markAsUnread();
        return redirect()->back();
    }
    public function showAll()
    {
        $notifications = auth()->user()->notifications;

        return view('admin.notification_all', [
            'notifications' => $notifications
        ]);
    }
}
