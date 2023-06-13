<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifications;
class NotificationController extends Controller
{
    //

    public function sendNotification($notify_from_user,$notify_to_user,$notification,$type){
        try {
            $notification = new Notifications();
            $notification->$notify_from_user = $notify_from_user;
            $notification->$notify_to_user = $notify_to_user;
            $notification->notification = $notification;
            $notification->notification_type = $type;
            $notification->status = 'active';
            $save_notification = $notification->save();
            if($save_notification){
                return response()->json([
                    'status' => true,
                    'message' => 'Notification saved successfully'
                ]); 
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Notification not saved successfully'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error Occured to save notifications: '.$e->getMessage()
            ], 500);
        }

    }

    public function getAllNotifications() {
        try {
            $notifications = Notifications::where('status', '!=', 'deleted')
                            ->orderBy('id', 'desc')
                            ->get();
        
            return response()->json([
                'message' => 'Notifications fetched successfully.',
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error Occured to fetch notifications.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getNotifications(Request $request) {
        try {
            $notifications = Notifications::where('notify_to_user', $request->notify_to_user)->where('status', '!=', 'deleted')
                            ->orderBy('id', 'desc')
                            ->get();
        
            return response()->json([
                'status' => true,
                'message' => 'Notifications fetched successfully.',
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error Occured.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
