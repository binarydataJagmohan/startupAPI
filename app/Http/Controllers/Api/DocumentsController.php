<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Business;
use App\Models\BankDetails;
use App\Models\CoFounder;
use App\Models\About;
use App\Models\Contact;
use App\Models\Documents;
use Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\VerificationCode;
use Carbon\Carbon;


class DocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function basic_information(Request $request)
    {
        try {
            $userId = $request->user_id;
            $data  = Documents::where('user_id', $userId)->first();
            if($data){
                $data ->update($request->all());
                return response()->json(['status' => true, 'message' => 'Basic Details updated successfully', 'data' => ['data' => $data]], 200);
            }else{
                $data                  = new Documents();
                $data->user_id         = $userId;
                $data->pan_number      = $request->pan_number;
                $data->uid             = $request->uid;
                $data->dob             = $request->dob;
               //  $data->proof_img =basename($imagePath);
                // $data->proof_img       = $request->proof_img;
                if ($request->hasFile('proof_img')) { 
                    $file = $request->file('proof_img');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $data->proof_img = $filename;
                }
        
                $data->save();
                $user=User::where('id',$userId)->update(['reg_step_3'=>'1']);
               return response()->json(['status' => true, 'message' => 'Basic Details stored successfully', 'data' => ['documents_details' => $data]], 200);
            }
            //  $data= $request->all();
            // $imagePath = $request->file('image')->store('public/images');
            
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_basic_information(Request $request)
    {
        try {
            $userId = $request->id;
            $data  = Documents::where('user_id', $userId)->first();
            if ($data) {
                $data  = Documents::where('user_id', $userId)->first();
                    return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
                }
        } catch (\Exception $e) {
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
