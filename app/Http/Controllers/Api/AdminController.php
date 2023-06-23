<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\User;
use App\Models\TermsAndConditions;
use App\Models\PrivacyPolicies;
use App\Models\Business;
use App\Models\BankDetails;
use App\Models\CoFounder;
use App\Models\About;
use App\Models\Contact;
use App\Mail\EmailVerification;
use App\Models\BusinessUnit;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_all_users(Request $request){
        try {
            $data = User::where('role','!=', 'admin')->orderBy('created_at', 'desc')
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

     public function privacy_policies(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'privacy_policies' => [
                    'required',
                    'regex:/\S/', // Ensure there is at least one non-whitespace character
                ],
            ]);
               if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation error',
                       'errors' => $validator->errors(),
                    ], 422);
              } else {
    
    
            $user = User::where('role', 'admin')->first()->id;
            $checkforpresentdata = PrivacyPolicies::where('user_id',$user)->first();
            if (trim($request->privacy_policies) === '<p><br></p>') {
                return response()->json([
                    'status' => false,
                    'message' => 'Privacy Policies cannot be empty',
                ], 422);
            }
            if($checkforpresentdata){
                $terms = $checkforpresentdata->update([
                    'privacy_policies' => $request->privacy_policies,
                    'user_id' =>$user
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Privacy Policies updated successfully',
                    'data' => $terms
                ], 200);
            }else{
    
                if (trim($request->privacy_policies) === '<p><br></p>') {
                    return response()->json([
                        'status' => false,
                        'message' => 'Privacy Policies cannot be empty',
                    ], 422);
                }else
                if(!empty(trim($request->privacy_policies))){
                    $terms =  new PrivacyPolicies();
                    $terms->user_id = $user;
                    $terms->privacy_policies = $request->privacy_policies;
                    $terms->save();
            
                    return response()->json([
                        'status' => true,
                        'message' => 'Privacy Policies added successfully',
                        'data' => $terms
                    ], 200);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Privacy Policies cannot be empty',
                    ], 422);
                }
                
            }
            
          
        }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
     }

    public function terms_and_conditions(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'terms_and_conditions' => [
                'required',
                'regex:/\S/', // Ensure there is at least one non-whitespace character
            ],
        ]);
           if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                   'errors' => $validator->errors(),
                ], 422);
          } else {


        $user = User::where('role', 'admin')->first()->id;
        $checkforpresentdata = TermsAndConditions::where('user_id',$user)->first();
        if (trim($request->terms_and_conditions) === '<p><br></p>') {
            return response()->json([
                'status' => false,
                'message' => 'Terms and conditions cannot be empty',
            ], 422);
        }
        if($checkforpresentdata){
            $terms = $checkforpresentdata->update([
                'terms_and_conditions' => $request->terms_and_conditions,
                'user_id' =>$user
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Terms and conditions updated successfully',
                'data' => $terms
            ], 200);
        }else{

            if (trim($request->terms_and_conditions) === '<p><br></p>') {
                return response()->json([
                    'status' => false,
                    'message' => 'Terms and conditions cannot be empty',
                ], 422);
            }else
            if(!empty(trim($request->terms_and_conditions))){
                $terms =  new TermsAndConditions();
                $terms->user_id = $user;
                $terms->terms_and_conditions = $request->terms_and_conditions;
                $terms->save();
        
                return response()->json([
                    'status' => true,
                    'message' => 'Terms and conditions added successfully',
                    'data' => $terms
                ], 200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Terms and conditions cannot be empty',
                ], 422);
            }
            
        }
        
      
    }
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error occurred',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function get_privacy_policies(Request $request){
    try{
        $privacy_policies = PrivacyPolicies::where('user_id',1)->first()->privacy_policies;
        return response()->json([
            'status' => true,
            'message' => 'Privacy Policies get successfully',
            'data' => $privacy_policies
        ], 200);
         
} catch (\Exception $e) {
    return response()->json([
        'status' => false,
        'message' => 'Error occurred',
        'error' => $e->getMessage()
    ], 500);
}
}

public function get_terms_and_conditions(Request $request){
    try{
            $termscondition = TermsAndConditions::where('user_id',1)->first()->terms_and_conditions;
            return response()->json([
                'status' => true,
                'message' => 'Terms and conditions get successfully',
                'data' => $termscondition
            ], 200);
             
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error occurred',
            'error' => $e->getMessage()
        ], 500);
    }
}
     public function update_admin_data(Request $request){
        try {
            // $validator = Validator::make($request->all(), [
            //     'name' => 'required|max:255',
            //     'email' => ['required', 'email', 'regex:/^[\w.%+-]+@[\w.-]+\.[a-zA-Z]{2,}$/i',],
            //     'phone' => 'required',
            //     'linkedin_url' =>[
            //         'required',
            //         'regex:/^(https?:\/\/)?([a-z]{2,3}\.)?linkedin\.com\/(in|company)\/[\w-]+$/'
            //     ],
                
            //     'gender' => 'required',
            //     'city' => 'required',
            //     'country' => 'required',
            
            // ]);
    
            // if ($validator->fails()) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Validation error',
            //         'errors' => $validator->errors(),
            //     ], 422);
            // } else {
                                        
                $admin = User::where('role','admin')->first();

                $admin->name = $request->input('name');
                $admin->email = $request->input('email');
                $admin->phone = $request->input('phone');
                $admin->city = $request->input('city');
                $admin->linkedin_url = $request->input('linkedin_url');
                $admin->country = $request->input('country');
                $admin->gender = $request->input('gender');
               
                if ($request->hasFile('profile_pic')) {
                    $randomNumber = mt_rand(1000000000, 9999999999);
                    $imagePath = $request->file('profile_pic');
                    $imageName = $randomNumber . $imagePath->getClientOriginalName();
                    $imagePath->move('images/profile', $imageName);
                    $admin->profile_pic = $imageName;
                }
    
                
                $admin->save();
              
    
                return response()->json([
                    'status' => true,
                    'message' => 'Admin has been updated successfully.',
                    'data' => $admin,
                ], 200);
            // }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy_user_data(Request $request,$id){

        try {
            $data = User::find($id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'User Deleted Successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }

     }
     public function destroy_investor_data(Request $request,$id){

        // try {
            $data = User::find($id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Investor Deleted Successfully.',
            ], 200);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Error occurred.',
        //         'error' => $e->getMessage()
        //     ], 500);
        // }

     }

     public function get_admin_data(Request $request){
        try{

            $data = User::where('role','admin')->first();

            if($data){
                return response()->json([

                    'status'=>false,
                    'message'=>'Admin Data fetch successfully',
                    'data'=>$data
                ],200);
            }else{
                return response()->json([
                    'status'=>false,
                    'message'=>'Something went wrong',
                    'data'=>''
                ]);
            }
        }catch(\Exception $e){

        }
     }
     public function get_single_investor(Request $request){
        try{
            $investor = User::where('id',$request->id)->first();
            if($investor){
                return response()->json([
                  'status'=>true,
                  'message'=>'Investor data fetch successfully',
                  'data'=>$investor


                ],200);
            }else{
                return response()->json([
                    'status'=>false,
                    'message'=>'investor not fetch successfully',
                    'data'=>''
                ],400);
            }
            

        }catch(\Exception $e){

        }
     }
     
     

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_fund(Request $request,$id){

        try {
            $data = BusinessUnit::find($id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Record Deleted Successfully.',
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_single_fund(Request $request){
        try {
            $user = BusinessUnit::where('id', $request->id)->first();
            if ($user) {
                return response()->json(['status' => true, 'message' => "Single data fetching successfully", 'data' => $user], 200);
            } 
        } catch (\Exception $e) {
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_all_active_funds(Request $request)
    {
        try {
            $funds= BusinessUnit::where('status','open')->orderBy('created_at', 'desc')->get();
            return response()->json([
                'status' => true,
                'message' => 'test',
                "data"=>$funds
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
    public function total_count_active_funds(Request $request)
    {
        try {
            $funds= BusinessUnit::where('status','open')->count();
            return response()->json([
                'status' => true,
                'message' => 'test',
                "data"=>$funds
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
        //
    }
}
