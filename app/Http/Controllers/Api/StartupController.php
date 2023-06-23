<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\User;
use App\Models\Business;
use App\Models\BankDetails;
use App\Models\CoFounder;
use App\Models\About;
use App\Models\BusinessUnit;
use App\Models\Contact;
use Mail;
use Sse\SSE;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\VerificationCode;
use Carbon\Carbon;

class StartupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function personal_information(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                // 'country_code' => 'required|string',
                'phone' => 'required',
                'gender' => 'required',
                'city' => 'required',
                'country' => 'required',
                'linkedin_url' => 'required|url',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                // Store the user in the database
                $user = User::find($request->id);
                $user->email = $request->email;
                $user->gender = $request->gender;
                $user->linkedin_url = $request->linkedin_url;
                $user->gender = $request->gender;
                $user->city = $request->city;
                $user->phone = $request->phone;
                // $user->country_code = $request->country_code;
                $user->country = $request->country;
                $user->reg_step_1 = '1';
                $user->save();

                return response()->json(['status' => true, 'message' => 'Profile updated successfully', 'data' => ['user' => $user]], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json(['success' => true, 'message' => 'Error Occuring.'], 500);
        }
    }

    public function get_fund_raise_count(Request $request){
        $count = BusinessUnit::where('status', 'open')->whereNotNull('fund_id')->count();

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
        
    public function update_personal_information(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                // 'country_code' => 'required|string',
                'email' => ['required', 'email', 'regex:/^[\w.%+-]+@[\w.-]+\.[a-zA-Z]{2,}$/i', 'unique:users,email,'.$request->id],
                'phone' => 'required',
                'gender' => 'required',
                'city' => 'required',
                'country' => 'required',
                'linkedin_url' =>[
                    'required',
                    'regex:/^(https?:\/\/)?([a-z]{2,3}\.)?linkedin\.com\/(in|company)\/[\w-]+$/'
                ],
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                // Store the user in the database
                $user = User::find($request->id);
                $user->update([
                    $user->email = $request->email,
                    $user->gender = $request->gender,
                    $user->linkedin_url = $request->linkedin_url,
                    $user->gender = $request->gender,
                    $user->city = $request->city,
                    $user->phone = $request->phone,
                    // $user->country_code = $request->country_code;
                    $user->country = $request->country,
                    $user->reg_step_1 = '1'

                ]);

              
                $user->save();

                return response()->json(['status' => true, 'message' => 'Profile updated successfully', 'data' => ['user' => $user]], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json(['success' => true, 'message' => 'Error Occuring.'], 500);
        }
    }

    public function business_information_update(Request $request, $userid)
    {
        try {
            $validator = Validator::make($request->all(), [
                'business_name' => 'required',
                'reg_businessname' => 'required',
                'stage' => 'required',
                'kyc_purposes' => 'required|gt:0',
                'startup_date' => 'required',
                'website_url' => 'required',
                'description' => 'required',
                'tagline' => 'required',
                'sector' => 'required',
                'type' => 'required',
                'logo' => 'required',
                
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }
    
            $userId = $userid;
            $data = Business::where('user_id', $userId)->first();
    
            if ($data) {
                $data->business_name = $request->business_name;
                $data->reg_businessname = $request->reg_businessname;
                $data->website_url = $request->website_url;
                $data->stage = $request->stage;
                $data->startup_date = $request->startup_date;
                $data->description = $request->description;
                $data->cofounder = $request->cofounder;
                $data->kyc_purposes = $request->kyc_purposes;
                $data->tagline = $request->tagline;
                $data->sector = $request->sector;
                $data->type = $request->type;
                $data->updated_at = Carbon::now();
                
    
                if ($request->hasFile('logo')) {
                    $file = $request->file('logo');
                    $filename =time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $data->logo = $filename;
                }
    
                $data->save();
    
                return response()->json([
                    'status' => true,
                    'message' => 'Business Details Updated successfully',
                    'data' => ['data' => $data],
                ], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_single_startup(Request $request,$id){
       
        try {
            $user = User::where(['id'=> $request->id,'role' => 'startup'])->firstOrFail();
            if ($user) {
                return response()->json(['status' => true, 'message' => "single startup data fetching successfully", 'data' => $user], 200);
            } 
            //  {
            //     return response()->json(['status' => false, 'message' => "There has been error for fetching the single", 'data' => ""], 400);
            // }
        } catch (\Exception $e) {
        }
  

   } 

   public function get_startup_count(Request $request){

    try{
          $data = User::where('role','startup')->count();
          if($data){
          return response()->json([
            'status' => true,
            'message'=>'Startups Count get Succcessfully ',
            'data'=> $data
          ],200);
          }else{
            return response()->json([
                'status' => false,
                'message'=>'Startups Count does not get Succcessfully ',
                'data'=> 0
              ],404);
          }

    }catch(\Exception $e){
        return response()->json([
          'status'=>false,
          'message'=>'error occurred',
          'error'=>$e->getMessage()

        ],500);
    }

   }
    public function business_information(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'business_name' => 'required',
                'reg_businessname' => 'required',
                'stage' => 'required',
                'startup_date' => 'required',
                'website_url' => 'required',
                'description' => 'required',
                // 'cofounder' => 'required',
                'kyc_purposes' => 'required',
                'tagline' => 'required',
                'sector' => 'required',
                'type'=> 'required',
                'logo' => [
                    Rule::requiredIf(function () use ($request) {
                        // Check if proof_img is already present in the database
                        $existingLogo = Business::where('user_id', $request->user_id)
                            ->whereNotNull('logo')
                            ->first();
            
                        return !$existingLogo;
                    }),
                    'image',
                    'mimes:jpeg,png,jpg',
                    'max:20480', // Adjust the file size limit if needed
                ],
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $userId = $request->user_id;
            $data  = Business::where('user_id', $userId)->first();
            if ($data) {
                // $data ->update($request->all());
                $data->update([
                    'business_name' => $request->business_name,
                    'reg_businessname' => $request->reg_businessname,
                    'website_url' => $request->website_url,
                    'stage' => $request->stage,
                    'startup_date' => $request->startup_date,
                    'description' => $request->description,
                    'cofounder' => $request->cofounder,
                    'kyc_purposes' => $request->kyc_purposes,
                    'tagline' => $request->tagline,
                    'sector' => $request->sector,
                    'type' => $request->type, 
                    'updated_at' => Carbon::now(),
                ]);
                if ($request->hasFile('logo')) {
                    $file = $request->file('logo');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $data->logo = $filename;
                    $data->save();
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Business Details Updated successfully',
                    'data' => ['data' => $request->type]
                ], 200);
            } else {
                $data = new Business();
                $data->user_id = $userId;


                $data->business_name = $request->business_name;
                $data->reg_businessname = $request->reg_businessname;
                $data->website_url = $request->website_url;
                $data->stage = $request->stage;
                $data->startup_date = $request->startup_date;
                $data->description = $request->description;
                $data->cofounder = $request->cofounder;
                $data->kyc_purposes = $request->kyc_purposes;
                $data->tagline = $request->tagline;
                $data->sector = $request->sector;
                $data->type = $request->type; // add this line
                $data->updated_at = Carbon::now();

                if ($request->hasFile('logo')) {
                    $file = $request->file('logo');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $data->logo = $filename;
                }

                $data->save();

                $user = User::where('id', $userId)->update(['reg_step_2' => '1']);

                return response()->json([
                    'status' => true,
                    'message' => 'Business Details stored successfully',
                    'data' => ['data' => $data]
                ], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_business_information(Request $request)
    {
        try {
            $userId = $request->id;
            $data  = Business::where('user_id', $userId)->first();
            if ($data) {
               
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            }
           
        } catch (\Exception $e) {
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_all_startup(Request $request)
    {
        try {
            $data = User::select('users.*', 'business_details.business_name', 'business_details.stage')
                ->join('business_details', 'users.id', '=', 'business_details.user_id')
                ->where(['role' => 'startup', 'is_profile_completed' => '1'])
                ->orderBy('created_at', 'desc')
                ->get();;

            if ($data) {
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateApprovalStatus(Request $request, $id)
    {
        try {
            $startup = User::where(['id' => $id, 'role' => 'startup'])->firstOrFail();
            $startup->approval_status = $request->input('approval_status');
            $startup->save();

            // $mail['name']= $startup->name;
            $mail['email'] = $startup->email;
            $mail['title'] = "Approval Mail";
            $mail['body'] =  "Your Account has been approved Successfully. ";

            Mail::send('email.approvedEmail', ['mail' => $mail], function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['title']);
            });

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateApprovalStage(Request $request, $id)
    {
        try {
            $data = Business::where('user_id', $id)->first();
            $data->stage = $request->input('stage');
            $data->save();

            return response()->json([
                'status' => true,
                'message' => 'Stage Updated Successfully.',
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
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $startup = User::find($id)->delete();
        if (!$startup) {
            
            return response()->json([
                'status'=>false,
                'message' => 'Business not found.'], 404);
        }
        
        return response()->json([
            'status'=>true,
            'message' => 'Business deleted successfully.']);
    }


    public function get_all_business_details()
    {
        try {
            $data = Business::leftJoin('business_units', 'business_details.id', '=', 'business_units.business_id')
                ->select('business_details.*', 'business_units.*')
              ->get();
            if ($data) {
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get_single_business_details($id)
    {
        try {
            $data = Business::leftJoin('business_units', 'business_units.business_id', '=', 'business_details.id')
                ->select('business_details.*', 'business_units.*')
                ->where(['business_details.id'=>$id])
                ->first();

            if ($data) {
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get_buisness_id($id)
    {
        try {
            $data = Business::where('user_id',$id)->first();
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function fund_raise_information_store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'total_units' => 'required',
                'minimum_subscription' => 'required',
                'avg_amt_per_person' => 'required',
                'tenure' => 'required',
                'repay_date' => 'required',
                'closed_in' => 'required',
                'resource' => 'required',
                // 'status' => 'required',
                'xirr'  =>'required',
                
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                // return response()->json(['status' => true, 'message' => "Data Store successfully", 'data' => $request->all()], 200);
              if($request->id){
                $data = BusinessUnit::where('id',$request->id)->first();
                // $data->business_id=$request->business_id;
                // $data->fund_id='STARTUP-'.$fund_id;
                $data->total_units = $request->total_units;
                $data->minimum_subscription= $request->minimum_subscription;
                $data->avg_amt_per_person= $request->avg_amt_per_person;
                $data->tenure=$request->tenure;
                $data->repay_date= $request->repay_date;
                $data->closed_in=$request->closed_in;
                $data->resource=$request->resource;
                $data->status= 'open';
                $data->xirr=$request->xirr;
                $data->amount= $request->amount;
                $data->no_of_units=$request->total_units;
                $data->desc= $request->desc;
                if ($request->hasFile('agreement')) {
                    $file = $request->file('agreement');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('pdf/');
                    $file->move($filepath, $filename);
                    $data->agreement = $filename;
                }
                if ($request->hasFile('invoice')) {
                    $file = $request->file('invoice');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('pdf/');
                    $file->move($filepath, $filename);
                    $data->invoice = $filename;
                }
                if ($request->hasFile('pdc')) {
                    $file = $request->file('pdc');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('pdf/');
                    $file->move($filepath, $filename);
                    $data->pdc = $filename;
                }
                $data->save();
                return response()->json(['status' => true, 'message' => "Data Updated Successfully.","data"=>$data], 200);
              }else{
                $data = BusinessUnit::where('business_id', $request->business_id)->get();

                // Check if any existing data has a status of 'open'
                // $hasOpenStatus = $data->contains(function ($item) {
                //     return $item->status === 'open';
                // });
                
                $hasOpenStatus = $data->contains(function($item){
                   return $item->status ==='open';
                });
                if ($hasOpenStatus) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Sorry, you have already rasied funds.',
                        'data' => ''
                    ], 404);
                }
                $fund_id = rand(100000, 999999);
                $data = new BusinessUnit();
                $data->business_id=$request->business_id;
                $data->fund_id='STARTUP-'.$fund_id;
                $data->total_units = $request->total_units;
                $data->minimum_subscription= $request->minimum_subscription;
                $data->avg_amt_per_person= $request->avg_amt_per_person;
                $data->tenure=$request->tenure;
                $data->repay_date= $request->repay_date;
                $data->closed_in=$request->closed_in;
                $data->resource=$request->resource;
                $data->status= 'open';
                $data->xirr=$request->xirr;
                $data->amount= $request->amount;
                $data->no_of_units=$request->total_units;
                $data->desc= $request->desc;
                if ($request->hasFile('agreement')) {
                    $file = $request->file('agreement');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('pdf/');
                    $file->move($filepath, $filename);
                    $data->agreement = $filename;
                }
                if ($request->hasFile('invoice')) {
                    $file = $request->file('invoice');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('pdf/');
                    $file->move($filepath, $filename);
                    $data->invoice = $filename;
                }
                if ($request->hasFile('pdc')) {
                    $file = $request->file('pdc');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('pdf/');
                    $file->move($filepath, $filename);
                    $data->pdc = $filename;
                }
                    $data->save();
                  
                 return response()->json(['status' => true, 'message' => "Data Store successfully", 'data' => $data], 200);
                
               
              }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get_all_funds($id)
    {
        try {
            $data = BusinessUnit::where('business_id',$id)->get();
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updateFundStatus(Request $request, $id)
    {
        try {
            $data = BusinessUnit::where(['id' => $id])->firstOrFail();
            $data->status = $request->input('status');
            $data->save();
            return response()->json([
                'status' => true,
                'message' => 'Status Updated Successfully.',
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
    public function updateStatus(Request $request, $id)
    {
        try {
            $startup = User::where(['id' => $id, 'role' => 'startup'])->firstOrFail();
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

    
    

 
}
