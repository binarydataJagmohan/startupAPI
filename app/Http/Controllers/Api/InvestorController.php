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
use Mail;
use App\Models\InvestorTerms;
class InvestorController extends Controller
{

    public function investor_type_information(Request $request)
    {
        try {
              $data=User::find($request->id);
              $data->investorType=$request->investorType;
              $data->reg_step_2 = '1';
              // $data->is_profile_completed = '1';
              $data->save();
        
            return response()->json([
                'status' => true,
                'message' => 'Data updated Successfully.',
                'data' => ['data' =>$data]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function get_investor_type_information(Request $request)
    {
        try {
            $user = User::where('id', $request->id)->first();
            if ($user) {
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $user], 200);
            } else {
                return response()->json(['status' => false, 'message' => "There has been error for fetching the single", 'data' => ""], 400);
            }
        } catch (\Exception $e) {
        }

    }
    public function angel_investor_terms(Request $request)
    {
        try {
            $userId = $request->user_id;
            $data = InvestorTerms::where('user_id', $userId)->first();
            
            if ($data) {
                $data->category = $request->category;
                $data->principal_residence = $request->category == "1" ? $request->principal_residence : 0;
                $data->cofounder = $request->category == "1" ? $request->cofounder : 0;
                $data->prev_investment_exp = $request->category == "1" ? $request->prev_investment_exp : 0;
                $data->experience = $request->category == "1" ? $request->experience : 0;
                $data->net_worth = $request->category == "2" ? $request->net_worth : 0;
                $data->no_requirements = $request->category == "3" ? $request->no_requirements : 0;
                $data->save();
                
                return response()->json([
                    'status' => true,
                    'message' => 'Profile has been Updated Successfully',
                    'data' => ['data' => $data]
                ], 200);
            } else {
                $data = new InvestorTerms();
                $data->user_id = $userId;
                $data->category = $request->category;
                $data->principal_residence = $request->category == "1" ? $request->principal_residence : 0;
                $data->cofounder = $request->category == "1" ? $request->cofounder : 0;
                $data->prev_investment_exp = $request->category == "1" ? $request->prev_investment_exp : 0;
                $data->experience = $request->category == "1" ? $request->experience : 0;
                $data->net_worth = $request->category == "2" ? $request->net_worth : 0;
                $data->no_requirements = $request->category == "3" ? $request->no_requirements : 0;
                $data->save();

              $user=User::where('id',$userId)->update(['reg_step_3' => '1','reg_step_4' => '1','is_profile_completed' =>'1']);
              $mail['email'] = $data->email;
              $mail['title'] = "Profile Completed";
              $mail['body'] =  "Profile has been Completed Successfully.";
              Mail::send('email.InvestorProfileCompleted', ['mail' => $mail], function ($message) use ($mail) {
                  $message->to($mail['email'])->subject($mail['title']);
              });
                
                return response()->json([
                    'status' => true,
                    'message' => 'Profile has been Completed Successfully.',
                    'data' => ['data' => $data]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function get_angel_investor_terms(Request $request)
    {
        try {
            $data = InvestorTerms::where('user_id', $request->id)->first();

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

    public function accredited_investor_terms(Request $request)
    {
        try {
            $userId = $request->user_id;
            $data = InvestorTerms::where('user_id', $userId)->first();
            
            if ($data) {
                $data->category = $request->category;
                $data->annual_income = $request->category == "1" ? $request->annual_income : 0;
                $data->financial_net_worth = $request->category == "1" ? $request->financial_net_worth : 0;
                $data->financial_annual_net_worth = $request->category == "1" ? $request->financial_annual_net_worth : 0;
                $data->foreign_annual_income = $request->category == "1" ? $request->foreign_annual_income : 0;
                $data->foreign_net_worth= $request->category == "2" ? $request->foreign_net_worth: 0;
                $data->foreign_annual_net_worth = $request->category == "3" ? $request->foreign_annual_net_worth : 0;
                $data->corporate_net_worth= $request->category == "3" ? $request->corporate_net_worth: 0;
                $data->save();

                $user=User::where('id',$userId)->update(['reg_step_3' => '1','reg_step_4' => '1','is_profile_completed' =>'1']);
                
                return response()->json([
                    'status' => true,
                    'message' => 'Profile has been Updated Successfully',
                    'data' => ['data' => $data]
                ], 200);
            } else {
                $data = new InvestorTerms();
                $data->user_id = $userId;
                $data->category = $request->category;
                $data->annual_income = $request->category == "1" ? $request->annual_income : 0;
                $data->financial_net_worth = $request->category == "1" ? $request->financial_net_worth : 0;
                $data->financial_annual_net_worth = $request->category == "1" ? $request->financial_annual_net_worth : 0;
                $data->foreign_annual_income = $request->category == "1" ? $request->foreign_annual_income : 0;
                $data->foreign_net_worth = $request->category == "2" ? $request->foreign_net_worth : 0;
                $data->foreign_annual_net_worth = $request->category == "3" ? $request->foreign_annual_net_worth : 0;
                $data->corporate_net_worth = $request->category == "3" ? $request->corporate_net_worth : 0;
                $data->save();
                
                $user=User::where("id",$userId);
              $mail['email'] =$user->email;
              $mail['title'] = "Profile Completed";
              $mail['body'] =  "Profile has been Completed Successfully.";
              Mail::send('email.InvestorProfileCompleted', ['mail' => $mail], function ($message) use ($mail) {
                  $message->to($mail['email'])->subject($mail['title']);
              });
                return response()->json([
                    'status' => true,
                    'message' => 'Profile has been Completed Successfully.',
                    'data' => ['data' => $data]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get_accredited_investor_terms(Request $request)
    {
        try {
            $data = InvestorTerms::where('user_id', $request->id)->first();

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

    public function get_all_investors(Request $request){
         try {
            $data = User::where(['role'=>'investor','is_profile_completed'=>'1'])->get();

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

    public function updateApprovalStatus(Request $request, $id)
    {
        try {
            $data = User::where(['id' => $id, 'role' => 'investor'])->firstOrFail();
            $data->approval_status = $request->input('approval_status');
            $data->save();
            $mail['email'] = $data->email;
            $mail['title'] = "Approval Mail";
            $mail['body'] =  "Your Account has been approved Successfully. ";

            Mail::send('email.approvedEmail', ['mail' => $mail], function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['title']);
            });

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
