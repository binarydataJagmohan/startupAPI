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
use App\Models\Payments;

class InvestorController extends Controller
{

    public function investor_type_information(Request $request)
    {
        try {
            $data = User::find($request->id);
            $data->investorType = $request->investorType;
            $data->reg_step_2 = '1';
            $data->save();
            
            $investor = InvestorTerms::where('user_id', $request->id)->first();
            if ($investor) {
                $investor->category = $request->category == "1" ? $request->category : 0;
                $investor->principal_residence = $request->principal_residence == "1" ? $request->principal_residence : 0;
                $investor->cofounder = $request->cofounder == "1" ? $request->cofounder : 0;
                $investor->prev_investment_exp = $request->prev_investment_exp == "1" ? $request->prev_investment_exp : 0;
                $investor->experience = $request->experience == "1" ? $request->experience : 0;
                $investor->net_worth = $request->net_worth == "2" ? $request->net_worth : 0;
                $investor->no_requirements = $request->no_requirements == "3" ? $request->no_requirements : 0;

                $investor->annual_income = $request->annual_income == "4" ? $request->annual_income : 0;
                $investor->financial_net_worth = $request->financial_net_worth == "5" ? $request->financial_net_worth : 0;
                $investor->financial_annual_net_worth = $request->financial_annual_net_worth == "6" ? $request->financial_annual_net_worth : 0;
                $investor->foreign_annual_income = $request->foreign_annual_income == "7" ? $request->foreign_annual_income : 0;
                $investor->foreign_net_worth = $request->foreign_net_worth == "8" ? $request->foreign_net_worth : 0;
                $investor->foreign_annual_net_worth = $request->foreign_annual_net_worth == "9" ? $request->foreign_annual_net_worth : 0;
                $investor->corporate_net_worth = $request->corporate_net_worth == "10" ? $request->corporate_net_worth : 0;

                $investor->save();
                
                return response()->json([
                    'status' => true,
                    'message' => 'Profile has been Updated Successfully',
                    'data' => ''
                ], 200);
            } else {
                $investor = new InvestorTerms();
                $investor->user_id = $request->id;
                $investor->category = $request->category == "1" ? $request->category : 0;
                $investor->principal_residence = $request->principal_residence == "1" ? $request->principal_residence : 0;
                $investor->cofounder = $request->cofounder == "1" ? $request->cofounder : 0;
                $investor->prev_investment_exp = $request->prev_investment_exp == "1" ? $request->prev_investment_exp : 0;
                $investor->experience = $request->experience == "1" ? $request->experience : 0;
                $investor->net_worth = $request->net_worth == "2" ? $request->net_worth : 0;
                $investor->no_requirements = $request->no_requirements == "3" ? $request->no_requirements : 0;

                $investor->annual_income = $request->annual_income == "4" ? $request->annual_income : 0;
                $investor->financial_net_worth = $request->financial_net_worth == "5" ? $request->financial_net_worth : 0;
                $investor->financial_annual_net_worth = $request->financial_annual_net_worth == "6" ? $request->financial_annual_net_worth : 0;
                $investor->foreign_annual_income = $request->foreign_annual_income == "7" ? $request->foreign_annual_income : 0;
                $investor->foreign_net_worth = $request->foreign_net_worth == "8" ? $request->foreign_net_worth : 0;
                $investor->foreign_annual_net_worth = $request->foreign_annual_net_worth == "9" ? $request->foreign_annual_net_worth : 0;
                $investor->corporate_net_worth = $request->corporate_net_worth == "10" ? $request->corporate_net_worth : 0;

                $investor->save();

              $user=User::where('id',$request->id)->update(['reg_step_3' => '1','reg_step_4' => '1','is_profile_completed' =>'1']);
              $mail['email'] = $data->email;
              $mail['title'] = "Profile Completed";
              $mail['body'] =  "Profile has been Completed Successfully.";
                return response()->json([
                    'status' => true,
                    'message' => 'Profile has been Completed Successfully.',
                    'data' => ['data' => $data]
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data updated Successfully.',
                'data' => ['data' => $data]
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

    public function updateInvestorInformation(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                // 'country_code' => 'required|string',
                'email' => ['required', 'email', 'regex:/^[\w.%+-]+@[\w.-]+\.[a-zA-Z]{2,}$/i', 'unique:users,email,' . $request->id],
                'phone' => 'required',
                'gender' => 'required',
                'city' => 'required',
                'country' => 'required',
                'linkedin_url' => [
                    'required',
                    // 'regex:/^(https:\/\/)?(www\.)?linkedin\.com\/(in\/[a-zA-Z0-9_-]+|company\/[a-zA-Z0-9_-]+|[a-zA-Z0-9_-]+\/?)\/?$/'
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

                $response = [
                    'status' => true,
                    'message' => 'Profile updated successfully',
                    'data' => ['user' => $user],
                ];

                return response()->json($response, 200)
                    ->header('Location', url('get-all-investors'))
                    ->header('Content-Type', 'application/json');
            }
        } catch (\Exception $e) {
        }
    }
    public function angel_investor_terms(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category' => 'required', // Validate the "category" field
                'principal_residence' => 'required_if:category,1', // Validate the "principal_residence" field if the "category" is 1
                'prev_investment_exp' => 'required_if:category,1', // Validate the "prev_investment_exp" field if the "category" is 1
                'cofounder' => 'required_if:category,1', // Validate the "cofounder" field if the "category" is 1
                'experience' => 'required_if:category,1', // Validate the "experience" field if the "category" is 1
                'net_worth' => 'required_if:category,2', // Validate the "net_worth" field if the "category" is 2
                'no_requirements' => 'required_if:category,3', // Validate the "no_requirements" field if the "category" is 3
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please Select a Category.',
                    'errors' => $validator->errors(),
                ], 200);
            } else {
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

                    $user = User::where('id', $userId)->update(['reg_step_3' => '1', 'reg_step_4' => '1', 'is_profile_completed' => '1']);
                    $mail['email'] = $data->email;
                    $mail['title'] = "Profile Completed";
                    $mail['body'] =  "Profile has been Completed Successfully.";
                    //   Mail::send('email.InvestorProfileCompleted', ['mail' => $mail], function ($message) use ($mail) {
                    //       $message->to($mail['email'])->subject($mail['title']);
                    //   });

                    return response()->json([
                        'status' => true,
                        'message' => 'Profile has been Completed Successfully.',
                        'data' => ['data' => $data]
                    ], 200);
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

            $validator = Validator::make($request->all(), [
                'category' => 'required',
                'annual_income' => 'required_if:category,1',
                'financial_net_worth' => 'required_if:category,1',
                'financial_annual_net_worth' => 'required_if:category,1',
                'foreign_annual_income' => 'required_if:category,1',
                'foreign_net_worth' => 'required_if:category,2',
                'foreign_annual_net_worth' => 'required_if:category,3',
                'corporate_net_worth' => 'required_if:category,3'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please Select a Category.',
                    'errors' => $validator->errors(),
                ], 200);
            } else {
                $userId = $request->user_id;
                $data = InvestorTerms::where('user_id', $userId)->first();

                if ($data) {
                    $data->category = $request->category;
                    $data->annual_income = $request->category == "1" ? $request->annual_income : 0;
                    $data->financial_net_worth = $request->category == "1" ? $request->financial_net_worth : 0;
                    $data->financial_annual_net_worth = $request->category == "1" ? $request->financial_annual_net_worth : 0;
                    $data->foreign_annual_income = $request->category == "1" ? $request->foreign_annual_income : 0;
                    $data->foreign_net_worth = $request->category == "2" ? $request->foreign_net_worth : 0;
                    $data->foreign_annual_net_worth = $request->category == "3" ? $request->foreign_annual_net_worth : 0;
                    $data->corporate_net_worth = $request->category == "3" ? $request->corporate_net_worth : 0;
                    $data->save();

                    $user = User::where('id', $request->user_id)->update(['reg_step_3' => '1', 'reg_step_4' => '1', 'is_profile_completed' => '1']);

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
                    User::where('id', $userId)->update(['reg_step_3' => '1', 'reg_step_4' => '1', 'is_profile_completed' => '1']);
                    $user = User::where("id", $userId)->first();
                    $mail['email'] = $user->email;
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

    public function get_all_investors(Request $request)
    {
        try {
            $data = User::where(['role' => 'investor', 'is_profile_completed' => '1'])->orderBy('created_at', 'desc')
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

    public function get_investor_count(Request $request)
    {
        try {
            $data = User::where(['role' => 'investor', 'is_profile_completed' => '1'])->count();
            if ($data) {
                return response()->json([

                    'status' => true,
                    'message' => 'Investor Count get Successfully',
                    'data' => $data
                ]);
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
            if ($data->approval_status === 'approved') {
                $mail['user']  = $data;
                $mail['email'] = $data->email;
                $mail['title'] = "Approval Mail";
                $mail['body'] =  "Your Account has been approved Successfully. ";
                Mail::send('email.approvedEmail', ['mail' => $mail], function ($message) use ($mail) {
                    $message->to($mail['email'])->subject($mail['title']);
                });
            }
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
    // get_total_subscriber_count
    public function get_total_subscriber_count(Request $request)
    {
        $count = Payments::where('id', $request->id)->count();

        try {
            if ($count) {
                return response()->json([

                    'status' => true,
                    'message' => 'Count get Successfully',
                    'data' => $count,
                ]);
            } else {
                return response()->json([

                    'status' => false,
                    'message' => 'Count not get Successfully',
                    'data' => '',
                ]);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}
