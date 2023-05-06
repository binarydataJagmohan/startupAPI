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
use App\Models\BusinessUnit;
use App\Models\Contact;
use Mail;
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
                $user->phone =
                    $request->phone;
                $user->country_code = $request->country_code;
                $user->country = $request->country;
                $user->reg_step_1 = '1';
                $user->save();

                return response()->json(['status' => true, 'message' => 'Profile updated successfully', 'data' => ['user' => $request->country]], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json(['success' => true, 'message' => 'Error Occuring.'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function business_information(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'business_name' => 'required',
                'reg_businessname' => 'required',
                'stage' => 'required',
                'startup_date' => 'required',
                'website_url' => 'required|url',
                'description' => 'required',
                // 'cofounder' => 'required',
                'kyc_purposes' => 'required',
                'tagline' => 'required',
                'sector' => 'required',
                'type'=> 'required',
                // 'logo' => 'required',
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
                $data  = Business::where('user_id', $userId)->first();
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            }
            // $data = Business::where('user_id', $request->id)->first();
            // if ($data) {
            //     return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            // } else {
            //     return response()->json(['status' => false, 'message' => "There has been error for fetching the business data.", 'data' => ""], 400);
            // }
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
    public function destroy($id)
    {
        $startup = Business::find($id);
        if (!$startup) {
            return response()->json(['message' => 'Business not found.'], 404);
        }
        $startup->delete();
        return response()->json(['message' => 'Business deleted successfully.']);
    }


    public function get_all_business_details()
    {
        try {
            $data = Business::leftJoin('business_units', 'business_details.id', '=', 'business_units.business_id')
                ->select('business_details.*', 'business_units.avg_amt_per_person', 'business_units.minimum_subscription', 'business_units.closed_in', 'business_units.total_units','business_units.no_of_units','business_units.tenure')
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
                ->where('business_details.id', $id)
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
                'status' => 'required',
                'xirr'  =>'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            } else {
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
                $data->status= $request->status;
                $data->xirr=$request->xirr;
                $data->amount= $request->amount;
                $data->total_no_units=$request->total_units;
                $data->save();
              
             return response()->json(['status' => true, 'message' => "Data Store successfully", 'data' => $data], 200);
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

    public function update_investor_status(Request $request, $id)
    {
        try {
            $data = User::where(['id' => $id, 'role' => 'investor'])->firstOrFail();
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

 
}
