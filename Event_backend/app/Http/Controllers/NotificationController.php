<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
   public function index(Request $request)
{
    $user = $request->user();
    $notifications = $user->notifications()->latest()->get();

    return response()->json($notifications);
}

public function markAsRead($notificationId)
{
    $notification = Notification::where('id', $notificationId)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();

    if ($notification->envoye_le === null) {
        $notification->envoye_le = now();
        $notification->save();
    }

    return response()->json(['message' => 'Notification marquée comme lue']);
}
public function show($id)
{
    $notification = Notification::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    return response()->json($notification);
}
}