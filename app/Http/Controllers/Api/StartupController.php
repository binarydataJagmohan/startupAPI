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
use App\Models\Ifinworth;
use Sse\SSE;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\VerificationCode;
use Carbon\Carbon;
use App\Models\Payments;
use Illuminate\Support\Facades\Mail;

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
                //'gender' => 'required',
                'city' => 'required',
                //'country' => 'required',
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

    public function get_fund_raise_count(Request $request)
    {
        $count = BusinessUnit::where('status', 'open')->whereNotNull('fund_id')->count();

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

    public function update_personal_information(Request $request)
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
                'kyc_purposes' => 'required',
                'startup_date' => 'required',
                'website_url' => 'required',
                'description' => 'required',
                'sector' => 'required',
                'pitch_deck' => 'required',

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
                $data->updated_at = Carbon::now();


                if ($request->hasFile('logo')) {
                    $file = $request->file('logo');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);
                    $data->logo = $filename;
                }
                if ($request->hasFile('pitch_deck')) {
                    $file = $request->file('pitch_deck');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);
                    $data->pitch_deck = $filename;
                    $data->save();
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
    // public function insert_ifinworth_details(Request $request)
    // {        
    //     try {
    //         $ifinworth = new Ifinworth();

    //         $ifinworth->startup_id = $request->startup_id;
    //         $ifinworth->round_of_ifinworth = $request->round_of_ifinworth;
    //         $ifinworth->ifinworth_currency = $request->ifinworth_currency;
    //         $ifinworth->ifinworth_amount = $request->ifinworth_amount;
    //         $ifinworth->pre_committed_ifinworth_currency = $request->pre_committed_ifinworth_currency;
    //         $ifinworth->pre_committed_ifinworth_amount = $request->pre_committed_ifinworth_amount;
    //         $ifinworth->pre_committed_investor = $request->pre_committed_investor;
    //         $ifinworth->accredited_investors = $request->accredited_investors;
    // $ifinworth->angel_investors = $request->angel_investors;
    // $ifinworth->regular_investors = $request->regular_investors;
    //         $ifinworth->other_funding_detail = $request->other_funding_detail;

    //         if ($request->hasFile('pitch_deck')) {
    //             $randomNumber = mt_rand(1000000000, 9999999999);
    //             $imagePath = $request->file('pitch_deck');
    //             $imageName = $randomNumber . $imagePath->getClientOriginalName();
    //             $imagePath->move(public_path('docs'), $imageName);
    //             $ifinworth->pitch_deck = $imageName;
    //         }

    //         if ($request->hasFile('one_pager')) {
    //             $randomNumber = mt_rand(1000000000, 9999999999);
    //             $imagePath = $request->file('one_pager');
    //             $imageName = $randomNumber . $imagePath->getClientOriginalName();
    //             $imagePath->move(public_path('docs'), $imageName);
    //             $ifinworth->one_pager = $imageName;
    //         }          
    //         if ($request->hasFile('previous_financials')) {
    //             $randomNumber = mt_rand(1000000000, 9999999999);
    //             $imagePath = $request->file('previous_financials');
    //             $imageName = $randomNumber . $imagePath->getClientOriginalName();
    //             $imagePath->move(public_path('docs'), $imageName);
    //             $ifinworth->previous_financials = $imageName;
    //         }

    //         if ($request->hasFile('latest_cap_table')) {
    //             $randomNumber = mt_rand(1000000000, 9999999999);
    //             $imagePath = $request->file('latest_cap_table');
    //             $imageName = $randomNumber . $imagePath->getClientOriginalName();
    //             $imagePath->move(public_path('docs'), $imageName);
    //             $ifinworth->latest_cap_table = $imageName;
    //         }

    //         if ($request->hasFile('other_documents')) {
    //             $randomNumber = mt_rand(1000000000, 9999999999);
    //             $imagePath = $request->file('other_documents');
    //             $imageName = $randomNumber . $imagePath->getClientOriginalName();
    //             $imagePath->move(public_path('docs'), $imageName);
    //             $ifinworth->other_documents = $imageName;
    //         }
    //         $savedata = $ifinworth->save();

    //         if ($savedata) {
    //             return response()->json(['status' => true, 'message' => "Data storred successfully", 'data' => $savedata], 200);
    //         } else {
    //             return response()->json(['status' => false, 'message' => "There has been an error ", 'data' => ""], 200);
    //         }
    //     } catch (\Exception $e) {
    //         throw new HttpException(500, $e->getMessage());
    //     }
    // }
    public function insert_ifinworth_details(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'round_of_ifinworth' => 'required',
                'ifinworth_currency' => 'required',
                'ifinworth_amount' => 'required',
                'pre_committed_ifinworth_currency' => 'required',
                'pre_committed_ifinworth_amount' => 'required',
                'pre_committed_investor' => 'required',
                'accredited_investors' => 'required',
                'other_funding_detail' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $startupId = $request->startup_id;
            $ifinworth = Ifinworth::where('startup_id', $startupId)->first();

            if (!$ifinworth) {
                $ifinworth = new Ifinworth();
            }

            $ifinworth->startup_id = $startupId;

            $fund_id = rand(100000, 999999);
            $ifinworth->ccsp_fund_id = 'CCSP-' . $fund_id;


            $ifinworth->round_of_ifinworth = $request->round_of_ifinworth;
            $ifinworth->ifinworth_currency = $request->ifinworth_currency;
            $ifinworth->ifinworth_amount = $request->ifinworth_amount;
            $ifinworth->pre_committed_ifinworth_currency = $request->pre_committed_ifinworth_currency;
            $ifinworth->pre_committed_ifinworth_amount = $request->pre_committed_ifinworth_amount;
            $ifinworth->pre_committed_investor = $request->pre_committed_investor;
            $ifinworth->accredited_investors = $request->accredited_investors;
            $ifinworth->angel_investors = $request->angel_investors;
            $ifinworth->regular_investors = $request->regular_investors;
            $ifinworth->other_funding_detail = $request->other_funding_detail;
            $this->processFileUpload($request, 'pitch_deck', $ifinworth, 'pitch_deck');
            $this->processFileUpload($request, 'one_pager', $ifinworth, 'one_pager');
            $this->processFileUpload($request, 'previous_financials', $ifinworth, 'previous_financials');
            $this->processFileUpload($request, 'latest_cap_table', $ifinworth, 'latest_cap_table');
            $this->processFileUpload($request, 'other_documents', $ifinworth, 'other_documents');

            $savedata = $ifinworth->save();

            if ($savedata) {
                return response()->json(['status' => true, 'message' => "Data stored successfully", 'data' => $savedata], 200);
            } else {
                return response()->json(['status' => false, 'message' => "There has been an error", 'data' => ""], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    private function processFileUpload(Request $request, $fieldName, $model, $attribute)
    {
        if ($request->hasFile($fieldName)) {
            $randomNumber = mt_rand(1000000000, 9999999999);
            $imagePath = $request->file($fieldName);
            $imageName = $randomNumber . $imagePath->getClientOriginalName();
            $imagePath->move(public_path('docs'), $imageName);
            $model->$attribute = $imageName;
        }
    }

    public function get_startup_ifinworth_detail(Request $request)
    {

        try {
            $ifinworth = Ifinworth::where('startup_id', $request->id)->first();
            if ($ifinworth) {
                return response()->json(['status' => true, 'message' => "single data fetching successfully", 'data' => $ifinworth], 200);
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
    public function get_single_startup(Request $request, $id)
    {

        try {
            $user = User::where(['id' => $request->id, 'role' => 'startup'])->firstOrFail();
            if ($user) {
                return response()->json(['status' => true, 'message' => "single startup data fetching successfully", 'data' => $user], 200);
            }
            //  {
            //     return response()->json(['status' => false, 'message' => "There has been error for fetching the single", 'data' => ""], 400);
            // }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function get_startup_count(Request $request)
    {

        try {
            $data = User::where('role', 'startup')->count();
            if ($data) {
                return response()->json([
                    'status' => true,
                    'message' => 'Startups Count get Succcessfully ',
                    'data' => $data
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Startups Count does not get Succcessfully ',
                    'data' => 0
                ], 404);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'error occurred',
                'error' => $e->getMessage()

            ], 500);
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
                'sector' => 'required',
                // 'pitch_deck' => [
                //     Rule::requiredIf(function () use ($request) {
                //         $existingPitchDeck = Business::where('user_id', $request->user_id)
                //             ->whereNotNull('pitch_deck')
                //             ->first();

                //         return !$existingPitchDeck;
                //     }),
                //     'mimetypes:application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                //     'max:20480',
                // ],

            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $userId = $request->user_id;
            $data = Business::where('user_id', $userId)->first();
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
                    // 'type' => $request->type,
                    'updated_at' => Carbon::now(),
                ]);
                if ($request->hasFile('logo')) {
                    $file = $request->file('logo');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);
                    $data->logo = $filename;
                    $data->save();
                }
                if ($request->hasFile('pitch_deck')) {
                    $file = $request->file('pitch_deck');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);
                    $data->pitch_deck = $filename;
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
                $data->updated_at = Carbon::now();

                if ($request->hasFile('logo')) {
                    $file = $request->file('logo');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);
                    $data->logo = $filename;
                }
                if ($request->hasFile('pitch_deck')) {
                    $file = $request->file('pitch_deck');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);
                    $data->pitch_deck = $filename;
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
            $data = Business::where('user_id', $userId)->first();
            if ($data) {

                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
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
                ->get();

            if ($data) {
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
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
            if ($startup->approval_status === "approved") {
                $mail['user'] = $startup;
                $mail['email'] = $startup->email;
                $mail['title'] = "Approval Mail";
                $mail['body'] = "Your Account has been approved Successfully. ";

                Mail::send('email.approvedEmail', ['mail' => $mail], function ($message) use ($mail) {
                    $message->to($mail['email'])->subject($mail['title']);
                });
            }
            return response()->json([
                'status' => true,
                'message' => 'Status Updated Successfully.',
                'data' => $startup
            ], 200);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
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
            throw new HttpException(500, $e->getMessage());
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
    public function destroy(Request $request, $id)
    {
        try {
            $startup = User::find($id)->delete();
            if (!$startup) {

                return response()->json([
                    'status' => false,
                    'message' => 'Startup not found.'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Startup deleted successfully.'
            ]);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
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
            throw new HttpException(500, $e->getMessage());
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
                ->where(['business_details.id' => $id])
                ->latest('business_units.created_at')
                ->first();
            return response()->json(['status' => true, 'message' => "Data fetched successfully", 'data' => $data], 200);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get_single_closed_business_details($id)
    {
        try {
            $data = Business::leftJoin('business_units', 'business_units.business_id', '=', 'business_details.id')
                ->select('business_details.*', 'business_units.*')
                ->where('business_details.id', $id)
                ->first();

            return response()->json(['status' => true, 'message' => "Data fetched successfully", 'data' => $data], 200);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
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
            $data = Business::where('user_id', $id)->first();
            return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function fund_raise_information_store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'total_units' => 'required',
                'minimum_subscription' => 'required',
                // 'avg_amt_per_person' => 'required',
                'tenure' => 'required',
                'repay_date' => 'required',
                'closed_in' => 'required',
                'resource' => 'required',
                // 'status' => 'required',
                'xirr' => 'required',
                'desc' => 'required',

            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                // return response()->json(['status' => true, 'message' => "Data Store successfully", 'data' => $request->all()], 200);

                if ($request->id) {
                    $data = BusinessUnit::where('id', $request->id)->first();
                    $data->business_id = $request->business_id;
                    // $data->fund_id='STARTUP-'.$fund_id;
                    $data->total_units = $request->total_units;
                    $data->minimum_subscription = $request->minimum_subscription;
                    $data->avg_amt_per_person = $request->avg_amt_per_person;
                    $data->tenure = $request->tenure;
                    $data->repay_date = $request->repay_date;
                    $data->closed_in = $request->closed_in;
                    $data->resource = $request->resource;
                    $data->status = 'open';
                    $data->xirr = $request->xirr;
                    $data->amount = $request->amount;
                    $data->no_of_units = $request->total_units;
                    $data->desc = $request->desc;
                    $data->type = $request->type;
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
                    return response()->json(['status' => true, 'message' => "Data Updated Successfully.", "data" => $data], 200);
                } else {
                    $data = BusinessUnit::where('business_id', $request->business_id)->get();

                    // Check if any existing data has a status of 'open'
                    // $hasOpenStatus = $data->contains(function ($item) {
                    //     return $item->status === 'open';
                    // });

                    $hasOpenStatus = $data->contains(function ($item) {
                        return $item->status === 'open';
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
                    $data->business_id = $request->business_id;
                    $data->fund_id = 'STARTUP-' . $fund_id;
                    $data->total_units = $request->total_units;
                    $data->minimum_subscription = $request->minimum_subscription;
                    $data->avg_amt_per_person = $request->avg_amt_per_person;
                    $data->tenure = $request->tenure;
                    $data->repay_date = $request->repay_date;
                    $data->closed_in = $request->closed_in;
                    $data->resource = $request->resource;
                    $data->status = 'open';
                    $data->xirr = $request->xirr;
                    $data->amount = $request->amount;
                    $data->no_of_units = $request->total_units;
                    $data->desc = $request->desc;
                    $data->type = $request->type;
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

                    return response()->json(['status' => true, 'message' => "Fund raised successfully.", 'data' => $data], 200);
                }
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
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
            $data = BusinessUnit::where('business_id', $id)->get();
            return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
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
            $data->closed_in = Carbon::now();
            $data->save();
            return response()->json([
                'status' => true,
                'message' => 'Status Updated Successfully.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
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
            throw new HttpException(500, $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTotalCountOfFund(Request $request, $id)
    {
        try {
            $data = BusinessUnit::where(['business_id' => $id])->count();
            // echo $data;
            // die;
            return response()->json([
                'status' => true,
                'message' => 'Count Fetched Successfully.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTotalCountOfUnits(Request $request, $id)
    {
        try {
            $data = BusinessUnit::where(['business_id' => $id, 'status' => 'open'])->get();
            // echo $data;
            // die;
            return response()->json([
                'status' => true,
                'message' => 'Data Fetched Successfully.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get_business_information_business_id(Request $request)
    {
        try {
            $data = Business::where(['id' => $request->id])->first();
            return response()->json([
                'status' => true,
                'message' => 'Data Fetched Successfully.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get_all_invested_fund_details(Request $request)
    {
        try {
            $data = Payments::join('business_details', 'payments.business_id', 'business_details.id')
                ->join('business_units', 'business_details.id', 'business_units.business_id')
                ->where('payments.user_id', $request->id)
                ->get();
            if ($data) {
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
