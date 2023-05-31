<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\User;
use App\Models\Business;
use App\Models\BankDetails;
use App\Models\CoFounder;
use App\Models\About;
use App\Models\Contact;
use Mail;
use App\Mail\EmailVerification;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DB;
use App\Mail\ResetPasswordMail;
use App\Models\PasswordReset;

class UserController extends Controller
{
 public function userRegister(Request $request)
{
   try {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|max:16',
            'role' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 200);
        } else {
            // Store the user in the database
            $user = new User();
            $user->name = $request->firstname . " " . $request->lastname;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role     = $request->role;
            $user->profile_pic=  "default.png";
            $data = $user->save();
           
            $token = JWTAuth::fromUser($user);

    
           
            return response()->json(['status' => true, 'message' => 'Verification link has been sent to your email.', 'data' => ['user' => $user, $token]], 200);
            
            $mailtoken = Str::random(40);
            $domain = env('NEXT_URL_LOGIN');
            $url = $domain . '/?token=' . $mailtoken;
            $mail['url'] = $url;
            $mail['email'] = $request->email;
            $mail['title'] = "Verify Your Account";
            $mail['body'] = "Please click on below link to verify your Account";
            $user->where('id',$user->id)->update(['email_verification_token'=>$mailtoken,'email_verified_at'=>Carbon::now()]);
            Mail::send('email.emailVerify', ['mail' => $mail], function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['title']);
            });
        }
    } catch (\Exception $e) {
        throw new HttpException(500, $e->getMessage());
        return response()->json(['success' => true, 'msg' => 'User has not been Register Successfully.'], 500);
    }
}

public function updateUser(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => ['required', 'email', 'regex:/^[\w.%+-]+@[\w.-]+\.[a-zA-Z]{2,}$/i', 'unique:users,email,'.$id],
                'phone' => 'required',
                'linkedin_url' =>[
                    'required',
                    'regex:/^(https?:\/\/)?([a-z]{2,3}\.)?linkedin\.com\/(in|company)\/[\w-]+$/'
                ],
                'status' => 'required',
                'gender' => 'required',
                'city' => 'required',
                'country' => 'required',
                'role' => 'required|string',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                // Update the user in the database
                // dd($request->hasFile('profile_pic'));                            
                $user = User::findOrFail($id);
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->phone = $request->input('phone');
                $user->status = $request->input('status');
                $user->city = $request->input('city');
                $user->linkedin_url = $request->input('linkedin_url');
                $user->country = $request->input('country');
                $user->gender = $request->input('gender');
                $user->role = $request->input('role');
    
                
                $user->save();
              
    
                return response()->json([
                    'status' => true,
                    'message' => 'User has been updated successfully.',
                    'data' => ['user' => $user],
                ], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
    

    public function user_login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                $credentials = $request->only('email', 'password');
                $token = JWTAuth::attempt($credentials);
                if (!$token) {
                    return response()->json([
                        'status' => false,
                        'error' => '',
                        'message' => 'Invalid Email and Password',
                    ], 200);
                }
                $user = Auth::user();
                return response()->json([
                    'status' => true,
                    'message' => 'User logged in successfully',
                    'user' => $user,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json(['success' => true, 'msg' => 'User is not authorized to log in successfully.'], 500);
        }
    }
    
    public function update_profile(Request $request)
    {
        try {
            $user = User::find($request->id);
            $user->name =  $request->name;
            $user->phone = $request->phone;
            $user->gender =  $request->gender;
            $user->city = $request->city;
            $user->country = $request->country;
            $user->linkedin_url = $request->linkedin_url;
            if ($request->hasFile('profile_pic')) {
                $randomNumber = mt_rand(1000000000, 9999999999);
                $imagePath = $request->file('profile_pic');
                $imageName = $randomNumber . $imagePath->getClientOriginalName();
                $imagePath->move('images/profile', $imageName);
                $user->profile_pic = $imageName;
            }
            $savedata = $user->save();
            if ($savedata) {
                return response()->json(['status' => true, 'message' => "Profile has been updated succesfully", 'data' => $savedata], 200);
            } else {
                return response()->json(['status' => false, 'message' => "There has been error for updating the profile", 'data' => ""], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
 
    public function update_bank_detail(Request $request)
    {
        try {
            $bank = BankDetails::find($request->id);
            $bank->user_id = $request->user_id;
            $bank->business_id = $request->business_id;
            $bank->bank_name = $request->bank_name;
            $bank->account_holder = $request->account_holder;
            $bank->account_no = $request->account_no;
            $bank->ifsc_code = $request->ifsc_code;
            $savedata = $bank->save();
            if ($savedata) {
                return response()->json(['status' => true, 'message' => "Bank detail has been updated succesfully", 'data' => $savedata], 200);
            } else {
                return response()->json(['status' => false, 'message' => "There has been error for updating the bank detail", 'data' => ""], 400);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
    public function business_detail_update(Request $request)
    {
        try {
            $user = User::where('id', $request->id)->first();
            if ($user) {
                $business = Business::find($request->id);
                $business->business_name = $request->business_name;
                $business->user_id = $user->id;
                $business->reg_businessname = $request->reg_businessname;
                $business->website_url = $request->website_url;
                $business->sector = $request->sector;
                $business->stage = $request->stage;
                $business->startup_date = $request->startup_date;
                if ($request->hasFile('logo')) {
                    $randomNumber = mt_rand(1000000000, 9999999999);
                    $imagePath = $request->file('logo');
                    $imageName = $randomNumber . $imagePath->getClientOriginalName();
                    $imagePath->move('images/profile', $imageName);
                    $business->logo = $imageName;
                }
                $business->description = $request->description;
                $savedata = $business->save();
            }
            if ($savedata) {
                return response()->json(['status' => true, 'message' => "Business detail has been stored succesfully", 'data' => $savedata], 200);
            } else {
                return response()->json(['status' => false, 'message' => "There has been error for storing the business detail", 'data' => ""], 400);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
    public function document_upload(Request $request)
    {
        try {
            $user = User::where('id', $request->id)->first();
            if ($user) {
                $update = User::find($request->id);
                $update->proof1_no = $request->proof1_no;
                $update->proof2_no = $request->proof2_no;
                if ($request->hasFile('proof1_pic')) {
                    $randomNumber = mt_rand(1000000000, 9999999999);
                    $imagePath = $request->file('proof1_pic');
                    $imageName = $randomNumber . $imagePath->getClientOriginalName();
                    $imagePath->move('images/document', $imageName);
                    $update->proof1_pic = $imageName;
                }
                if ($request->hasFile('proof2_pic')) {
                    $randomNumber = mt_rand(1000000000, 9999999999);
                    $imagePath = $request->file('proof2_pic');
                    $imageName = $randomNumber . $imagePath->getClientOriginalName();
                    $imagePath->move('images/document', $imageName);
                    $update->proof2_pic = $imageName;
                }
                $savedata = $update->save();
            }
            if ($savedata) {
                return response()->json(['status' => true, 'message' => "Document  has been uploaded succesfully", 'data' => $savedata], 200);
            } else {
                return response()->json(['status' => false, 'message' => "There has been error for uploading the document", 'data' => ""], 400);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
    public function get_single_user(Request $request)
    {
        try {
            $user = User::where('id', $request->id)->first();
            if ($user) {
                return response()->json(['status' => true, 'message' => "single data fetching successfully", 'data' => $user], 200);
            } else {
                return response()->json(['status' => false, 'message' => "There has been error for fetching the single", 'data' => ""], 400);
            }
        } catch (\Exception $e) {
        }
    }
    public function save_contact(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email',
                'subject' => 'required|string',
                'message' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                // Store the user in the database
                $user = new Contact();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->subject = $request->subject;
                $user->message = $request->message;
                $user->save();

                $data = [
                    'name' => $user->name,
                    'email' => $user->email
                ];

                Mail::send('contactMail', ['data1' => $data], function ($message) use ($data) {
                    $message->from('demo93119@gmail.com', "StartUp");
                    $message->subject('Welcome to StartUp, ' . $data['name'] . '!');
                    $message->to($data['email']);
                });
                return response()->json(['status' => true, 'message' => 'Contact stored successfully', 'error' => '', 'data' => ''], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function join_to_invest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'invalid email error',
                    'errors' => $validator->errors(),
                ], 200);
            } else {
                return response()->json(['status' => true, 'message' => "valid email", 'data' => $request->all()], 200);
            }
        } catch (\Exception $e) {
        }
    }
    public function send_otp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid phone number',
                    'errors' => $validator->errors(),
                ], 200);
            }
            $user = User::find($request->id);
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found'], 404);
            }
            $user->phone = $request->phone;
            $user->save();
            
            $otp = VerificationCode::where('user_id', $user->id)->first();
            if ($otp) {
                $otp->otp = rand(1000, 9999);
                $otp->expire_at = Carbon::now()->addMinutes(1);
                $otp->save();
            } else {
                $otp = VerificationCode::create([
                    'user_id' => $user->id,
                    'otp' => rand(1000, 9999),
                    'expire_at' => Carbon::now()->addMinutes(1),
                ]);
            }
            $data = [
                'name' => $user->name,
                'otp' => $otp->otp,
                'email' => $user->email
            ];
            Mail::send('otpMail', ['data' => $data], function ($message) use ($user) {
                $message->from('demo93119@gmail.com', "StartUp");
                $message->subject('Welcome to StartUp, ' . $user['name'] . '!');
                $message->to($user['email']);
            });
            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully',
                'data' => $otp->otp,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error sending OTP',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function confirm_otp(Request $request)
    {
        try {
            $verificationCode = VerificationCode::where('user_id', 1)->where('otp', $request->otp)->first();
            $now = Carbon::now();
            if (!$verificationCode) {
                return response()->json(['status' => false, 'message' => "Your OTP is not correct", 'data' => ''], 200);
            } elseif ($verificationCode && $now->isAfter($verificationCode->expire_at)) {
                return response()->json(['status' => false, 'message' => "Your OTP has been expired", 'data' => ''], 200);
            }

            return response()->json(['status' => true, 'message' => "otp successfully confirmed", 'data' => ''], 200);
        } catch (\Exception $e) {
        }
    }

    public function reset_password(Request $request){
          
        try {
            $user =  User::where('email', $request->email)->first();
            if ($user) {
                $token = Str::random(40);
                $domain = env('NEXT_URL');
                $url = $domain . '/?userid='.$user->id.'&resettoken=' . $token;
                $data['url'] = $url;
                $data['email'] = $request->email;
                $data['title'] = "password reset";
                $data['body'] = "Please click on below link to reset your password";
                Mail::send('email.ResetPasswordMail', ['data' => $data], function ($message) use ($data) {
                    $message->to($data['email'])->subject($data['title']);
                });
                $datetime = Carbon::now()->format('Y-m-d H:i:s');
                PasswordReset::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'token' => $token,
                        'created_at' => $datetime,
                    ]
                );
                return response()->json(['status' => true, 'message' => 'Mail has been sent please check your email!'], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'Mail doesn`t not exist'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 200);
        }
    }
    public function check_user_reset_password_verfication(Request $request)
    {
        $resetData = PasswordReset::where('user_id', $request->id)->where('token', $request->token)->count();

        if ($resetData > 0) {
            return response()->json(['status' => true, ]);
        } else {
            return response()->json(['status' => false, 'message' => 'Invalid User Authoriztation']);
        }
    }

    public function updated_reset_password(Request $request)
   
     {
        try {
            $request->validate([
                'password' => 'required|string|min:8',
            ]);
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['status' => false, 'msg' => 'User not found'], 200);
            }
            $user->password = Hash::make($request->password);
            $user->new_password = $request->password;
            $user->update();
            PasswordReset::where('user_id', $request->user_id)->delete();
            return response()->json(['status' => true, 'message' => 'Password reset successful'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'message' => 'Password reset failed'], 500);
        }
    }

    public function check_user_email_verfication(Request $request)
    {
        try {

            $id = $request->id;
            $token = $request->token;

            $check = UserVerify::where('user_id', $id)->where('token', $token)->count();

            if ($check > 0) {

                $user = User::where('id', $id)->update([

                    'email_verified' => 1,
                    'email_verified_at' => Carbon::now(),
                ]);

                UserVerify::where('user_id', $id)->where('token', $token)->delete();

                return response()->json(['message' => "Email verifiy successfully", 'status' => true], 200);
            } else {

                return response()->json(['message' => "Email verfication failed", 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'msg' => 'Password reset failed'], 200);
        }
    }


    // For update user's status Active or Deactive
    public function updateUserStatus(Request $request, $id)
    {
        try {
            $startup = User::where(['id' => $id])->firstOrFail();
            $startup->status = $request->input('status');
            $startup->save();

            return response()->json([
                'status' => true,
                'message' => 'Status Updated Successfully.',
                'data' => $startup
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // updateUserRole
    public function updateUserRole(Request $request, $id)
    {
        try {
            $data = User::where('id', $id)->first();
            $data->role = $request->input('role');
            $data->save();

            return response()->json([
                'status' => true,
                'message' => 'Role Updated Successfully.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // updateUserCountry
    public function updateUserCountry(Request $request, $id)
    {
        try {
            $data = User::where('id', $id)->first();
            $data->country = $request->input('country');
            $data->save();

            return response()->json([
                'status' => true,
                'message' => 'Country Updated Successfully.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
