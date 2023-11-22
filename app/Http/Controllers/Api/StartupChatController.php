<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatGroups;
use App\Models\ChatGroupMembers;
use App\Models\ChatMessages;
use DB;
class StartupChatController extends Controller
{
    public function get_startup_message_data(Request $request)
    {
            $userId = $request->id;
            $userSideMessages = ChatMessages::select(
                'chat_messages.single_chat_id',
                'chat_messages.receiver_id',
                'sender.name AS sender_name',
                'sender.profile_pic AS sender_pic',
                'sender.role AS sender_role',
                'sender.id AS sender_id',
                'receiver.name AS recevier_name',
                'receiver.profile_pic AS recevier_pic',
                'receiver.role AS recevier_role',
                'receiver.id AS receiver_id',
                'chat_messages.group_id',
                'chat_groups.group_name',
                'chat_groups.image AS group_image',
                'chat_messages.status',
                'chat_messages.message_status',
                'chat_messages.message_type AS latest_chat_type',
                'chat_messages.message_attachment_type AS latest_type',
                'chat_messages.message_attachment AS message_attachment',
                //'sender.is_online',
                DB::raw('(SELECT COUNT(*) FROM chat_messages WHERE chat_messages.message_status = "unread" AND chat_messages.receiver_id = '.$userId.' AND chat_messages.sender_id = sender.id) as unreadcount'),
                DB::raw('CASE 
                    WHEN chat_messages.message_type = "single" THEN (
                        SELECT message 
                        FROM chat_messages AS cm
                        WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                        ORDER BY cm.created_at DESC 
                        LIMIT 1
                    )
                    WHEN chat_messages.message_type = "group" THEN (
                        SELECT message 
                        FROM chat_messages AS cm
                        WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                        ORDER BY cm.created_at DESC 
                        LIMIT 1
                    )
                    ELSE NULL
                END AS latest_message'),
                DB::raw('CASE 
                    WHEN chat_messages.message_type = "single" THEN (
                        SELECT created_at
                        FROM chat_messages AS cm
                        WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                        ORDER BY cm.created_at DESC 
                        LIMIT 1
                    )
                    WHEN chat_messages.message_type = "group" THEN (
                        SELECT created_at
                        FROM chat_messages AS cm
                        WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                        ORDER BY cm.created_at DESC 
                        LIMIT 1
                    )
                    ELSE NULL
                END AS latest_created_at'),
                DB::raw('CASE 
                    WHEN chat_messages.message_type = "single" THEN (
                        SELECT message_attachment_type 
                        FROM chat_messages AS cm
                        WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                        ORDER BY cm.created_at DESC 
                        LIMIT 1
                    )
                    WHEN chat_messages.message_type = "group" THEN (
                        SELECT message_attachment_type 
                        FROM chat_messages AS cm
                        WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                        ORDER BY cm.created_at DESC 
                        LIMIT 1
                    )
                    ELSE NULL
                END AS latest_type'),
            )
            ->join('users AS sender', function ($join) {
                $join->on('chat_messages.sender_id', '=', 'sender.id')
                    ->orWhere('chat_messages.receiver_id', '=', 'sender.id');
            })
            ->leftJoin('users AS receiver', function ($join) {
                 $join->on('chat_messages.receiver_id', '=', 'receiver.id')
                    ->orWhereNull('chat_messages.receiver_id');
            })

            ->leftJoin('chat_groups', 'chat_messages.group_id', '=', 'chat_groups.id')
            ->where(function ($query) use ($userId) {
                $query->Where('chat_messages.single_chat_id', '=', DB::raw('CONCAT(sender.id, '.$userId.')'))
                    ->orWhere('chat_messages.single_chat_id', '=', DB::raw("CONCAT(" . $userId . ", receiver.id)"))
                    ->orWhereIn('chat_messages.group_id', function ($subquery) use ($userId) {
                        $subquery->select('group_id')
                            ->from('chat_group_members')
                            ->where('member_id', $userId);
                           
                    });
            })
            ->groupBy(
                'chat_messages.single_chat_id',
                'chat_messages.group_id'
            )
            ->orderBy('latest_created_at', 'desc')
            ->get();

            $chat_type = $userSideMessages[0]->latest_chat_type;

            if($chat_type == 'single'){

                $first = $userSideMessages[0]->receiver_id;
                $second = $userSideMessages[0]->sender_id;

                $unquie = $first.$second;
                $unquie_two = $second.$first;

                $userChatMessages = ChatMessages::select('message', 'sender.name AS sender_name', 'receiver.name AS receiver_name', 'sender.profile_pic AS sender_pic', 'receiver.profile_pic AS receiver_pic', 'sender.role AS sender_role', 'receiver.role AS receiver_role', 'sender.id AS sender_id', 'receiver.id AS receiver_id', 'chat_messages.created_at as chatdate', 'message_attachment_type','message_attachment')
                ->join('users AS sender', 'chat_messages.sender_id', '=', 'sender.id')
                ->join('users AS receiver', 'chat_messages.receiver_id', '=', 'receiver.id')
                
                ->where('chat_messages.single_chat_id', $unquie)
                ->orWhere('chat_messages.single_chat_id', $unquie_two)
                ->get();

                $chat_member = 0;
            }

            if($chat_type == 'group'){

                $group_id = $userSideMessages[0]->group_id;

                $userChatMessages = ChatMessages::select('message', 'sender.name AS sender_name','sender.profile_pic AS sender_pic', 'sender.role AS sender_role', 'sender.id AS sender_id', 'chat_messages.created_at as chatdate', 'message_attachment_type','message_attachment')
                ->join('users AS sender', 'chat_messages.sender_id', '=', 'sender.id')
                ->Where('chat_messages.group_id', $group_id)
                ->get();

                $chat_member = ChatGroupMembers::select(
                        'name',
                        'role',
                        'users.id as user_id',
                        'role',
                        'profile_pic',
                        'group_admin_id'
              
                    )
                ->leftJoin('users', 'chat_group_members.member_id', '=', 'users.id')
                ->join('chat_groups', 'chat_group_members.group_id', '=', 'chat_groups.id')
                ->where('chat_group_members.group_id', $group_id)
                ->orderBy('chat_group_members.id', 'desc')
                ->get();

            }


           return response()->json([
                    'status' => true,
                    'userchatsider' => $userSideMessages,
                    'userchatdata' => $userChatMessages,
                    'chat_member' => $chat_member
                    // 'data'=> $single_chat_id
                ]);


            try {
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch user messages',
            ], 500);
        }
    }

    public function contact_user_by_startup(Request $request)
    {
        try {

            if($request->chat_type == 'single'){

                if($request->user_id != $request->receiver_id){
                   $receiver_id = $request->receiver_id;
                }
                 if($request->user_id != $request->sender_id){
                    $receiver_id = $request->sender_id;
                }
                $messgae = new ChatMessages();
                $messgae->sender_id =  $request->user_id;
                $messgae->receiver_id =  $receiver_id;
                $messgae->single_chat_id =  $request->single_chat_id;
                $messgae->message =  $request->message;
                $messgae->message_type =  $request->chat_type;
                $messgae->save();

                return response()->json([
                    'status' => true,
                    'message' => 'messgae has been sent successfully'
                ]);
            }  

            if($request->chat_type == 'group'){
                
                $messgae = new ChatMessages();
                $messgae->sender_id =  $request->user_id;
                $messgae->group_id =  $request->group_id;
                $messgae->message =  $request->message;
                $messgae->message_type =  $request->chat_type;
                $messgae->save();
            
               return response()->json([
                    'status' => true,
                    'message' => 'messgae has been sent successfully'
                ]);

            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user messages',
            ], 500);
        }
    }


    public function get_click_startup_user_chat_data(Request $request)
    {
        try {

            $userId = $request->id;
            $index = $request->index;
            $sort = $request->sort;
            $sorttype = $request->chat_sort_type;

            if(isset($index) && $request->latest_chat_type == 'single'){

                $Chat_message = ChatMessages::where('sender_id',$request->sender_id)->where('single_chat_id', $request->single_chat_id)->update(['message_status' => 'read']);
            }

            if (($sort == 'asc' || $sort == 'desc') && $sorttype == 'second') {

                $userSideMessages = ChatMessages::select(
                    'chat_messages.single_chat_id',
                    'chat_messages.receiver_id',
                    'sender.name AS sender_name',
                    'sender.profile_pic AS sender_pic',
                    'sender.role AS sender_role',
                    'sender.id AS sender_id',
                    'receiver.name AS recevier_name',
                    'receiver.profile_pic AS recevier_pic',
                    'receiver.role AS recevier_role',
                    'receiver.id AS receiver_id',
                    'chat_messages.group_id',
                    'chat_groups.group_name',
                    'chat_groups.image AS group_image',
                    'chat_messages.status',
                    'chat_messages.message_status',
                    'chat_messages.message_type AS latest_chat_type',
                    'chat_messages.message_attachment AS message_attachment',
                    'chat_messages.message_attachment_type AS latest_type',
                    //'sender.is_online',
                        DB::raw('(SELECT COUNT(*) FROM chat_messages WHERE chat_messages.message_status = "unread" AND chat_messages.receiver_id = '.$userId.' AND chat_messages.sender_id = sender.id) as unreadcount'),
                        DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT message 
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT message 
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_message'),
                    DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT created_at
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT created_at
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_created_at'),

                    DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT message_attachment_type 
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT message_attachment_type
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_type'),
                    
                )
                ->join('users AS sender', function ($join) {
                    $join->on('chat_messages.sender_id', '=', 'sender.id')
                        ->orWhere('chat_messages.receiver_id', '=', 'sender.id');
                })
                ->leftJoin('users AS receiver', function ($join) {
                    $join->on('chat_messages.receiver_id', '=', 'receiver.id')
                        ->orWhereNull('chat_messages.receiver_id');
                })

                ->leftJoin('chat_groups', 'chat_messages.group_id', '=', 'chat_groups.id')
                ->where(function ($query) use ($userId) {
                    $query->Where('chat_messages.single_chat_id', '=', DB::raw('CONCAT(sender.id, '.$userId.')'))
                        ->orWhere('chat_messages.single_chat_id', '=', DB::raw("CONCAT(" . $userId . ", receiver.id)"))
                        ->orWhereIn('chat_messages.group_id', function ($subquery) use ($userId) {
                            $subquery->select('group_id')
                                ->from('chat_group_members')
                                ->where('member_id', $userId);
                            
                        });
                })
                ->groupBy(
                    'chat_messages.single_chat_id',
                    'chat_messages.group_id'
                )
                ->orderBy('latest_created_at', $sort)
                ->get();



                $chat_type = $request->latest_chat_type;


                if($chat_type == 'single'){

                    $userChatMessages = ChatMessages::select('message', 'sender.name AS sender_name', 'receiver.name AS receiver_name', 'sender.profile_pic AS sender_pic', 'receiver.profile_pic AS receiver_pic', 'sender.role AS sender_role', 'receiver.role AS receiver_role', 'sender.id AS sender_id', 'receiver.id AS receiver_id', 'chat_messages.created_at as chatdate', 'message_attachment_type', 'message_attachment')
                    ->join('users AS sender', 'chat_messages.sender_id', '=', 'sender.id')
                    ->join('users AS receiver', 'chat_messages.receiver_id', '=', 'receiver.id')
                    
                
                    ->Where('chat_messages.single_chat_id', $request->single_chat_id)
                    ->get();

                    $chat_member = 0;
                }

                if($chat_type == 'group'){

                    $group_id = $request->group_id;

                    $userChatMessages = ChatMessages::select('message', 'sender.name AS sender_name','sender.profile_pic AS sender_pic', 'sender.role AS sender_role', 'sender.id AS sender_id', 'chat_messages.created_at as chatdate', 'message_attachment_type', 'message_attachment')
                    ->join('users AS sender', 'chat_messages.sender_id', '=', 'sender.id')
                    ->Where('chat_messages.group_id', $group_id)
                    ->get();

                    $chat_member = ChatGroupMembers::select(
                            'name',
                            'role',
                            'users.id as user_id',
                            'role',
                            'profile_pic',
                            'group_admin_id'
                
                        )
                    ->leftJoin('users', 'chat_group_members.member_id', '=', 'users.id')
                    ->join('chat_groups', 'chat_group_members.group_id', '=', 'chat_groups.id')
                    ->where('chat_group_members.group_id', $group_id)
                    ->orderBy('chat_group_members.id', 'desc')
                    ->get();
                }   


            return response()->json([
                        'status' => true,
                        'userchatsider' => $userSideMessages,
                        'userchatdata' => $userChatMessages,
                        'chat_member' => $chat_member
                        // 'data'=> $single_chat_id
                    ]);

            }


            if ( $sort == 'unread' && $sorttype == 'second') {


                $userSideMessages = ChatMessages::select(
                    'chat_messages.single_chat_id',
                    'chat_messages.receiver_id',
                    'sender.name AS sender_name',
                    'sender.profile_pic AS sender_pic',
                    'sender.role AS sender_role',
                    'sender.id AS sender_id',
                    'receiver.name AS recevier_name',
                    'receiver.profile_pic AS recevier_pic',
                    'receiver.role AS recevier_role',
                    'receiver.id AS receiver_id',
                    'chat_messages.group_id',
                    'chat_groups.group_name',
                    'chat_groups.image AS group_image',
                    'chat_messages.status',
                    'chat_messages.message_status',
                    'chat_messages.message_type AS latest_chat_type',
                    'chat_messages.message_attachment_type AS latest_type',
                    'chat_messages.message_attachment AS message_attachment',
                    //'sender.is_online',
                        DB::raw('(SELECT COUNT(*) FROM chat_messages WHERE chat_messages.message_status = "unread" AND chat_messages.receiver_id = '.$userId.' AND chat_messages.sender_id = sender.id) as unreadcount'),
                        DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT message 
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT message 
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_message'),


                    DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT created_at
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT created_at
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_created_at'),

                    DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT type 
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT type 
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_type'),
                    
                )
                ->join('users AS sender', function ($join) {
                    $join->on('chat_messages.sender_id', '=', 'sender.id')
                        ->orWhere('chat_messages.receiver_id', '=', 'sender.id');
                })
                ->leftJoin('users AS receiver', function ($join) {
                    $join->on('chat_messages.receiver_id', '=', 'receiver.id')
                        ->orWhereNull('chat_messages.receiver_id');
                })

                ->leftJoin('chat_groups', 'chat_messages.group_id', '=', 'chat_groups.id')
                ->where(function ($query) use ($userId) {
                    $query->Where('chat_messages.single_chat_id', '=', DB::raw('CONCAT(sender.id, '.$userId.')'))
                        ->orWhere('chat_messages.single_chat_id', '=', DB::raw("CONCAT(" . $userId . ", receiver.id)"))
                        ->orWhereIn('chat_messages.group_id', function ($subquery) use ($userId) {
                            $subquery->select('group_id')
                                ->from('chat_group_members')
                                ->where('member_id', $userId);
                            
                        });
                })
                ->groupBy(
                    'chat_messages.single_chat_id',
                    'chat_messages.group_id'
                )
                ->orderByRaw('unreadcount DESC, latest_created_at DESC')
                ->get();

                $chat_type = $request->latest_chat_type;


                if($chat_type == 'single'){

                    $userChatMessages = ChatMessages::select(
                        'message',
                        'sender.name AS sender_name',
                        'receiver.name AS receiver_name',
                        'sender.profile_pic AS sender_pic',
                        'receiver.profile_pic AS receiver_pic',
                        'sender.role AS sender_role',
                        'receiver.role AS receiver_role',
                        'sender.id AS sender_id',
                        'receiver.id AS receiver_id',
                        'chat_messages.created_at as chatdate',
                        'chat_messages.message_attachment_type',
                        'chat_messages.message_attachment'
                    )
                    ->join('users AS sender', 'chat_messages.sender_id', '=', 'sender.id')
                    ->join('users AS receiver', 'chat_messages.receiver_id', '=', 'receiver.id')
                    ->get();

                    $chat_member = 0;
                }
                if($chat_type == 'group'){

                    $group_id = $request->group_id;

                    $userChatMessages = ChatMessages::select('message', 'sender.name AS sender_name','sender.profile_pic AS sender_pic', 'sender.role AS sender_role', 'sender.id AS sender_id', 'chat_messages.created_at as chatdate', 'message_attachment_type','message_attachment')
                    ->join('users AS sender', 'chat_messages.sender_id', '=', 'sender.id')
                    ->Where('chat_messages.group_id', $group_id)
                    ->get();

                    $chat_member = ChatGroupMembers::select(
                            'name',
                            'role',
                            'users.id as user_id',
                            'role',
                            'profile_pic',
                            'group_admin_id'
                
                        )
                    ->leftJoin('users', 'chat_group_members.member_id', '=', 'users.id')
                    ->join('chat_groups', 'chat_group_members.group_id', '=', 'chat_groups.id')
                    ->where('chat_group_members.group_id', $group_id)
                    ->orderBy('chat_group_members.id', 'desc')
                    ->get();
                }   
            return response()->json([
                        'status' => true,
                        'userchatsider' => $userSideMessages,
                        'userchatdata' => $userChatMessages,
                        'chat_member' => $chat_member
                        // 'data'=> $single_chat_id
                    ]);

            }

            if (($sort == 'asc' || $sort == 'desc') && $sorttype == 'first') {

                $userSideMessages = ChatMessages::select(
                    'chat_messages.single_chat_id',
                    'chat_messages.receiver_id',
                    'sender.name AS sender_name',
                    'sender.profile_pic AS sender_pic',
                    'sender.role AS sender_role',
                    'sender.id AS sender_id',
                    'receiver.name AS recevier_name',
                    'receiver.profile_pic AS recevier_pic',
                    'receiver.role AS recevier_role',
                    'receiver.id AS receiver_id',
                    'chat_messages.group_id',
                    'chat_groups.group_name',
                    'chat_groups.image AS group_image',
                    'chat_messages.status',
                    'chat_messages.message_status',
                    'chat_messages.message_type AS latest_chat_type',
                    'chat_messages.message_attachment_type AS latest_type',
                    'chat_messages.message_attachment AS message_attachment',
                    //'sender.is_online',
                        DB::raw('(SELECT COUNT(*) FROM chat_messages WHERE chat_messages.message_status = "unread" AND chat_messages.receiver_id = '.$userId.' AND chat_messages.sender_id = sender.id) as unreadcount'),
                        DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT message 
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT message 
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_message'),

                    DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT created_at
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT created_at
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_created_at'),

                    DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT message_attachment_type 
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT message_attachment_type 
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_type'),
                    
                )
                ->join('users AS sender', function ($join) {
                    $join->on('chat_messages.sender_id', '=', 'sender.id')
                        ->orWhere('chat_messages.receiver_id', '=', 'sender.id');
                })
                ->leftJoin('users AS receiver', function ($join) {
                    $join->on('chat_messages.receiver_id', '=', 'receiver.id')
                        ->orWhereNull('chat_messages.receiver_id');
                })

                ->leftJoin('chat_groups', 'chat_messages.group_id', '=', 'chat_groups.id')
                ->where(function ($query) use ($userId) {
                    $query->Where('chat_messages.single_chat_id', '=', DB::raw('CONCAT(sender.id, '.$userId.')'))
                        ->orWhere('chat_messages.single_chat_id', '=', DB::raw("CONCAT(" . $userId . ", receiver.id)"))
                        ->orWhereIn('chat_messages.group_id', function ($subquery) use ($userId) {
                            $subquery->select('group_id')
                                ->from('chat_group_members')
                                ->where('member_id', $userId);
                            
                        });
                })
                ->groupBy(
                    'chat_messages.single_chat_id',
                    'chat_messages.group_id'
                )
                ->orderBy('latest_created_at', $sort)
                ->get();



                $chat_type = $userSideMessages[0]->latest_chat_type;


                if($chat_type == 'single'){

                    $first = $userSideMessages[0]->receiver_id;
                    $second = $userSideMessages[0]->sender_id;

                    $unquie = $first.$second;
                    $unquie_two = $second.$first;

                    $userChatMessages = ChatMessages::select('message', 'sender.name AS sender_name', 'receiver.name AS receiver_name', 'sender.profile_pic AS sender_pic', 'receiver.profile_pic AS receiver_pic', 'sender.role AS sender_role', 'receiver.role AS receiver_role', 'sender.id AS sender_id', 'receiver.id AS receiver_id', 'chat_messages.created_at as chatdate', 'message_attachment_type','message_attachment')
                    ->join('users AS sender', 'chat_messages.sender_id', '=', 'sender.id')
                    ->join('users AS receiver', 'chat_messages.receiver_id', '=', 'receiver.id')
                    ->Where('chat_messages.single_chat_id', $unquie)
                    ->orWhere('chat_messages.single_chat_id', $unquie_two)
                    ->get();

                    $chat_member = 0;
                }

                if($chat_type == 'group'){

                    $group_id = $userSideMessages[0]->group_id;

                    $userChatMessages = ChatMessages::select('message', 'sender.name AS sender_name','sender.profile_pic AS sender_pic', 'sender.role AS sender_role', 'sender.id AS sender_id', 'chat_messages.created_at as chatdate', 'message_attachment_type','message_attachment')
                    ->join('users AS sender', 'chat_messages.sender_id', '=', 'sender.id')
                    ->Where('chat_messages.group_id', $group_id)
                    ->get();

                    $chat_member = ChatGroupMembers::select(
                            'name',
                            'role',
                            'users.id as user_id',
                            'role',
                            'profile_pic',
                            'group_admin_id'
                
                        )
                    ->leftJoin('users', 'chat_group_members.member_id', '=', 'users.id')
                    ->join('chat_groups', 'chat_group_members.group_id', '=', 'chat_groups.id')
                    ->where('chat_group_members.group_id', $group_id)
                    ->orderBy('chat_group_members.id', 'desc')
                    ->get();
                }   


            return response()->json([
                        'status' => true,
                        'userchatsider' => $userSideMessages,
                        'userchatdata' => $userChatMessages,
                        'chat_member' => $chat_member
                        // 'data'=> $single_chat_id
                    ]);

            }


            if ( $sort == 'unread' && $sorttype == 'first') {

                $userSideMessages = ChatMessages::select(
                    'chat_messages.single_chat_id',
                    'chat_messages.receiver_id',
                    'sender.name AS sender_name',
                    'sender.profile_pic AS sender_pic',
                    'sender.role AS sender_role',
                    'sender.id AS sender_id',
                    'receiver.name AS recevier_name',
                    'receiver.profile_pic AS recevier_pic',
                    'receiver.role AS recevier_role',
                    'receiver.id AS receiver_id',
                    'chat_messages.group_id',
                    'chat_groups.group_name',
                    'chat_groups.image AS group_image',
                    'chat_messages.status',
                    'chat_messages.message_status',
                    'chat_messages.message_type AS latest_chat_type',
                    'chat_messages.message_attachment_type AS latest_type',
                    'chat_messages.message_attachment AS message_attachment',
                    //'sender.is_online',
                        DB::raw('(SELECT COUNT(*) FROM chat_messages WHERE chat_messages.message_status = "unread" AND chat_messages.receiver_id = '.$userId.' AND chat_messages.sender_id = sender.id) as unreadcount'),
                        DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT message 
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT message 
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_message'),

                    DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT created_at
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT created_at
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_created_at'),

                    DB::raw('CASE 
                        WHEN chat_messages.message_type = "single" THEN (
                            SELECT message_attachment_type 
                            FROM chat_messages AS cm
                            WHERE cm.single_chat_id = chat_messages.single_chat_id AND cm.message_type = "single" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        WHEN chat_messages.message_type = "group" THEN (
                            SELECT message_attachment_type 
                            FROM chat_messages AS cm
                            WHERE cm.group_id = chat_messages.group_id AND cm.message_type = "group" 
                            ORDER BY cm.created_at DESC 
                            LIMIT 1
                        )
                        ELSE NULL
                    END AS latest_type'),
                    
                )
                ->join('users AS sender', function ($join) {
                    $join->on('chat_messages.sender_id', '=', 'sender.id')
                        ->orWhere('chat_messages.receiver_id', '=', 'sender.id');
                })
                ->leftJoin('users AS receiver', function ($join) {
                    $join->on('chat_messages.receiver_id', '=', 'receiver.id')
                        ->orWhereNull('chat_messages.receiver_id');
                })

                ->leftJoin('chat_groups', 'chat_messages.group_id', '=', 'chat_groups.id')
                ->where(function ($query) use ($userId) {
                    $query->Where('chat_messages.single_chat_id', '=', DB::raw('CONCAT(sender.id, '.$userId.')'))
                        ->orWhere('chat_messages.single_chat_id', '=', DB::raw("CONCAT(" . $userId . ", receiver.id)"))
                        ->orWhereIn('chat_messages.group_id', function ($subquery) use ($userId) {
                            $subquery->select('group_id')
                                ->from('chat_group_members')
                                ->where('member_id', $userId);
                            
                        });
                })
                ->groupBy(
                    'chat_messages.single_chat_id',
                    'chat_messages.group_id'
                )
                ->orderByRaw('unreadcount DESC, latest_created_at DESC')
                ->get();



                $chat_type = $userSideMessages[0]->latest_chat_type;


                if($chat_type == 'single'){

                    $first = $userSideMessages[0]->receiver_id;
                    $second = $userSideMessages[0]->sender_id;

                    $unquie = $first.$second;
                    $unquie_two = $second.$first;


                $userChatMessages = ChatMessages::select(
                        'message',
                        'sender.name AS sender_name',
                        'receiver.name AS receiver_name',
                        'sender.profile_pic AS sender_pic',
                        'receiver.profile_pic AS receiver_pic',
                        'sender.role AS sender_role',
                        'receiver.role AS receiver_role',
                        'sender.id AS sender_id',
                        'receiver.id AS receiver_id',
                        'chat_messages.created_at as chatdate',
                        'chat_messages.message_attachment_type',
                        'chat_messages.message_attachment'
                    )
                    ->join('users AS sender', 'chat_messages.sender_id', '=', 'sender.id')
                    ->join('users AS receiver', 'chat_messages.receiver_id', '=', 'receiver.id')
                    ->get();
                    
                    $chat_member = 0;
                }

                if($chat_type == 'group'){

                    $group_id = $userSideMessages[0]->group_id;

                    $userChatMessages = ChatMessages::select('message', 'sender.name AS sender_name','sender.profile_pic AS sender_pic', 'sender.role AS sender_role', 'sender.id AS sender_id', 'chat_messages.created_at as chatdate', 'message_attachment_type','message_attachment')
                    ->join('users AS sender', 'chat_messages.sender_id', '=', 'sender.id')
                    ->Where('chat_messages.group_id', $group_id)
                    ->get();

                    $chat_member = ChatGroupMembers::select(
                            'name',
                            'role',
                            'users.id as user_id',
                            'role',
                            'profile_pic',
                            'group_admin_id'
                
                        )
                    ->leftJoin('users', 'chat_group_members.member_id', '=', 'users.id')
                    ->join('chat_groups', 'chat_group_members.group_id', '=', 'chat_groups.id')
                    ->where('chat_group_members.group_id', $group_id)
                    ->orderBy('chat_group_members.id', 'desc')
                    ->get();
                }   


            return response()->json([
                        'status' => true,
                        'userchatsider' => $userSideMessages,
                        'userchatdata' => $userChatMessages,
                        'chat_member' => $chat_member
                        // 'data'=> $single_chat_id
                    ]);

            } 
          
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch user messages',
            ], 500);
        }
    }


    
    public function contact_user_by_startup_with_share_file(Request $request)
    {   
        $randomNumber = mt_rand(1000000000, 9999999999);

        $type = $request->type;

        $file = $request->data;
       
        if($request->chat_type == 'single'){

                if($request->user_id != $request->receiver_id){
                   $receiver_id = $request->receiver_id;
                }
                 if($request->user_id != $request->sender_id){
                    $receiver_id = $request->sender_id;
                }
                $messgae = new ChatMessages();
                $messgae->sender_id =  $request->user_id;
                $messgae->receiver_id =  $receiver_id;
                $messgae->single_chat_id =  $request->single_chat_id;
                $messgae->message_type =  $request->chat_type;

                if ($type === 'image') {
                    // Handle image file
                    $path = $file;
                    $name = $randomNumber . $path->getClientOriginalName();
                    $path->move('public/images/chat/images', $name);
                   
                } elseif ($type === 'pdf') {
                    // Handle PDF file
                    $path = $file;
                    $name = $randomNumber . $path->getClientOriginalName();
                    $path->move('public/images/chat/pdf', $name);

                   
                } elseif ($type === 'video') {
                    // Handle video file
                    $path = $file;
                    $name = $randomNumber . $path->getClientOriginalName();
                    $path->move('public/images/chat/video', $name);
                    
                }
                $messgae->message_attachment_type =  $type;
                $messgae->message_attachment =  $name;

                $messgae->save();

                return response()->json([
                    'status' => true,
                    'message' => 'messgae has been sent successfully'
                ]);
            }    

            if($request->chat_type == 'group'){
                
                $messgae = new ChatMessages();
                $messgae->sender_id =  $request->user_id;
                $messgae->group_id =  $request->group_id;
                $messgae->message_type =  $request->chat_type;

                if ($type === 'image') {
                // Handle image file
                $path = $file;
                $name = $randomNumber . $path->getClientOriginalName();
                $path->move('public/images/chat/images', $name);
                   
                } elseif ($type === 'pdf') {
                    // Handle PDF file
                    $path = $file;
                    $name = $randomNumber . $path->getClientOriginalName();
                    $path->move('public/images/chat/pdf', $name);

                   
                } elseif ($type === 'video') {
                    // Handle video file
                    $path = $file;
                    $name = $randomNumber . $path->getClientOriginalName();
                    $path->move('public/images/chat/video', $name);
                    
                }
                $messgae->message_attachment_type =  $type;
                $messgae->message_attachment =  $name;

                $messgae->save();
            
               return response()->json([
                    'status' => true,
                    'message' => 'messgae has been sent successfully'
                ]);

            }

       
            try {
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch user messages',
            ], 500);
        }

    }


     public function get_admin_data(Request $request)
    {
        try {
            $user = User::where('status','active')->where('role','=','admin')->first();
           
            return response()->json(['status' => true, 'message' => "all user data", 'data' => $user]);
           
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

}