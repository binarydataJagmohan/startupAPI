<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifications;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Mail;
use App\Models\Business;

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
        try {
            $notifications = Notifications::select('notifications.notify_msg','notifications.created_at','users.name')
                ->leftJoin('users','notifications.notify_from_user','users.id')
                ->where('notifications.notify_to_user', $id)
                ->where('notifications.status', '!=', 'deleted')
                ->orderBy('notifications.id', 'desc')
                ->get();
            
            return response()->json([
                'status' => true,
                'message' => 'Notifications fetched successfully.',
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    


    // getTotalCountOfNotifications
    public function getTotalCountOfNotifications($id){
        $count = Notifications::where(['notify_to_user'=>$id])->count();

        try{
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

        }catch(\Exception $e){
                throw new HttpException(500,$e->getMessage());
        }
    }


     // getCountOfUnreadNotifications
     public function getCountOfUnreadNotifications($id){
        $count = Notifications::where(['notify_to_user'=>$id,'each_read'=>'unread'])->count();

        try{
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

        }catch(\Exception $e){
                throw new HttpException(500,$e->getMessage());
        }
    }

    //Send Mail notifications to investors
    public function sendMailNotification(){
        try {
          
            $investors = User::where(['role' => 'investor'])->get();
            foreach ($investors as $investor) {
                $mail['domain'] = env('NEXT_URL_LOGIN');
                $mail['email'] = $investor->email;
                $mail['title'] = "Fund Raise Notification";
                $mail['body'] = "New Fund Raised By Startup.";
                $mail['username'] = $investor->name;
                Mail::send('email.fundInvestorNotification', ['mail' => $mail], function ($message) use ($mail) {
                    $message->to($mail['email'])->subject($mail['title']);
                });
            }
            return response()->json([
                'status' => true,
                'message' => 'Mail Notifications has been sent Successfully.',
                'data' => $investors
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error Occurred: ' . $e->getMessage()
            ], 500);
        }

    }

    public function destroy_notifications(Request $request,$id){

        try {
            $data = Notifications::where(['notify_to_user'=>$id,'each_read'=>'read'])->delete();

            return response()->json([
                'status' => true,
                'message' => 'Notifications Deleted Successfully.','data'=>$data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
     }
      public function investor_viewer(Request $request)
    {
        try {
            $data = Business::where('id', $request->business_id)->first();
            $user = User::where('id',$request->user_id)->first();
            if ($data) {
                $notification = new Notifications();
                $notification->notify_from_user = $request->user_id;
                $notification->notify_to_user = '1';
                $notification->notification_type = 'Viewer Notification';
                $notification->notify_msg = 'Great news! ' . $user->name .' is showing interest in the ' .$data->business_name .' Company.';
                $notification->each_read = 'unread';
                $notification->status = 'active';
                $notification->save();
                return response()->json([
                    'status' => true
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
