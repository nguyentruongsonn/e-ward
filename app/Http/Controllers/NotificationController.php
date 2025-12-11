<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    /**
     * Mark notifications as read by storing the current timestamp in session
     */
    public function markAsRead(Request $request)
    {
        // Store current timestamp as the last viewed time
        Session::put('notifications_last_viewed', now()->toDateTimeString());
        
        return response()->json([
            'success' => true,
            'message' => 'Notifications marked as read'
        ]);
    }
}
