<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Symfony\Component\HttpKernel\Exception\HttpException;
class NotificationController extends Controller
{
    //

    public function sendNotification(Request $request){
        try {
            $notification = new Notifications();
            $notification->notify_from_user = $request->notify_from_user;
            $notification->notify_to_user = $request->notify_to_user;
            $notification->notify_msg = $request->notify_msg;
            $notification->notification_type = $request->notification_type;
            $notification->status = 'active';
            $notification->each_read = 'unread';
            $save_notification = $notification->save();
    
            if ($save_notification) {
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
                'message' => 'Error Occurred: ' . $e->getMessage()
            ], 500);
        }

    }
    // updateNotification
    public function updateNotification($id){
        try {
            $notification = Notifications::where(['notify_to_user'=>$id])->update(['each_read'=>'read']);
            // $notification->each_read= "read";
            // $notification = $notification->save();
            if($notification){
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

    public function getNotifications($id) {
        // try {
            $notifications = Notifications::where('notify_to_user', $id)
                ->where('status', '!=', 'deleted')
                ->orderBy('id', 'desc')
                ->get();
            
            return response()->json([
                'status' => true,
                'message' => 'Notifications fetched successfully.',
                'data' => $notifications
            ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Error Occurred.',
        //         'error' => $e->getMessage()
        //     ], 500);
        // }
    }
    


    // getTotalCountOfNotifications
    public function getTotalCountOfNotifications($id){
        $count = Notifications::where(['notify_to_user'=>$id])->count();

        // try{
            if($count){
                return response()->json([
    
                    'status' =>true,
                    'message'=>'Count get Successfully',
                    'data' => $count,
                ]);
            }else{
                return response()->json([
    
                    'status' =>false,
                    'message'=>'Count not get Successfully',
                    'data' => '',
                ]);
        }

        // }catch(\Exception $e){
        //         throw new HttpException(500,$e->getMessage());
        // }
    }


     // getCountOfUnreadNotifications
     public function getCountOfUnreadNotifications($id){
        $count = Notifications::where(['notify_to_user'=>$id,'each_read'=>'unread'])->count();

        // try{
            if($count){
                return response()->json([
    
                    'status' =>true,
                    'message'=>'Count get Successfully',
                    'data' => $count,
                ]);
            }else{
                return response()->json([
    
                    'status' =>false,
                    'message'=>'Count not get Successfully',
                    'data' => '',
                ]);
        }

        // }catch(\Exception $e){
        //         throw new HttpException(500,$e->getMessage());
        // }
    }
}
