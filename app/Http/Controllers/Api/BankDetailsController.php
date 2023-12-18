<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\User;
use App\Models\Business;
use App\Models\BankDetails;
use App\Models\CoFounder;
use App\Models\About;
use App\Models\Contact;
use Illuminate\Support\Facades\Validator;
use Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\VerificationCode;
use Carbon\Carbon;

class BankDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bank_details(Request $request)
    {
        try {

            // $validator = Validator::make($request->all(), [
            //     'bank_name' => 'required',
            //     'account_holder' => 'required',
            //     'account_no' => 'required',
            //     'ifsc_code' => 'required',
            // ]);

            // if ($validator->fails()) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Validation error',
            //         'errors' => $validator->errors(),
            //     ], 422);
            // }


            $userId = $request->id;
            $data  = BankDetails::where('user_id', $userId)->first();
            if ($data) {
                $data->update($request->all());
                return response()->json(['status' => true, 'message' => 'Details updated successfully', 'data' => ['data' => $data]], 200);
            } else {
                $data = new BankDetails();
                $data->user_id = $userId;
                $data->bank_name = $request->bank_name;
                $data->account_holder = $request->account_holder;
                $data->account_no = $request->account_no;
                $data->ifsc_code = $request->ifsc_code;
                $data->save();

                $user = User::find($request->id);
                $user->reg_step_4 = '1';
                $user->is_profile_completed = '1';
                $user->save();

                $pdf_file_path = public_path('/pdf/file.pdf');

                $mail['username'] = $user->name;
                $mail['email'] = $user->email;
                $mail['title'] = "Profile Completed";
                $mail['body'] = "Profile has been Completed Successfully. ";
                $mail['pdf_file'] = $pdf_file_path;

                Mail::send('email.ProfileCompletedMail', ['mail' => $mail], function ($message) use ($mail) {
                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    $message->to($mail['email'])->subject($mail['title']);
                    $message->attach($mail['pdf_file']);
                });


                return response()->json([
                    'status' => true,
                    'message' => 'Bank details stored successfully',
                    'data' => ['bank_details' => $request->id]
                ], 200);
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_bank_information(Request $request)
    {
        try {
            $userId = $request->id;
            $data  = BankDetails::where('user_id', $userId)->first();
            if ($data) {
                $data  = BankDetails::where('user_id', $userId)->first();
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
