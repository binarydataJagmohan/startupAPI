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
use App\Models\DocumentUpload;
use App\Models\Documents;
use Illuminate\Validation\Rule;
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
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'pan_number' => 'required',
                'uid' => 'required',
                'dob' => 'required',
                'proof_img' => [
                    Rule::requiredIf(function () use ($request) {
                        // Check if proof_img is already present in the database
                        $existingProofImg = Documents::where('user_id', $request->user_id)
                            ->whereNotNull('proof_img')
                            ->first();

                        return !$existingProofImg;
                    }),
                    'file',
                    'mimetypes:application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'max:20480', // Adjust the file size limit if needed
                ],
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 400);
            }
            $data  = Documents::where('user_id', $userId)->first();
            if ($data) {
                $data->update([
                    'user_id' => $request->user_id,
                    'pan_number' => $request->pan_number, 'uid' => $request->uid, 'dob' => $request->dob
                ]);
                if ($request->hasFile('proof_img')) {
                    $file = $request->file('proof_img');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $data->proof_img = $filename;
                }

                $data->save();
                return response()->json(['status' => true, 'message' => 'Basic Details updated successfully', 'data' => ['data' => $data]], 200);
            } else {
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
                $user = User::where('id', $userId)->update(['reg_step_3' => '1']);
                return response()->json(['status' => true, 'message' => 'Basic Details stored successfully', 'data' => ['documents_details' => $data]], 200);
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

    public function upload_documents(Request $request)
    {
        try {


            $user_id = $request->user_id;

            $checkuserid = DocumentUpload::where('user_id', $request->user_id)->count();

            if ($checkuserid > 0) {

                $documents = DocumentUpload::where('user_id', $user_id)->first();

                if ($request->hasFile('pan_card_front')) {
                    $file = $request->file('pan_card_front');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->pan_card_front = $filename;
                }

                if ($request->hasFile('pan_card_back')) {
                    $file = $request->file('pan_card_back');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->pan_card_back = $filename;
                }

                if ($request->hasFile('adhar_card_front')) {
                    $file = $request->file('adhar_card_front');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->adhar_card_front = $filename;
                }

                if ($request->hasFile('adhar_card_back')) {
                    $file = $request->file('adhar_card_back');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->adhar_card_back = $filename;
                }
                $documents->save();

                return response()->json(['status' => true, 'message' => 'Basic Details updated successfully', 'data' => ['documents_details' => $documents]], 200);
            } else {

                $documents = new DocumentUpload();
                $documents->user_id = $request->user_id;
                if ($request->hasFile('pan_card_front')) {
                    $file = $request->file('pan_card_front');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->pan_card_front = $filename;
                }

                if ($request->hasFile('pan_card_back')) {
                    $file = $request->file('pan_card_back');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->pan_card_back = $filename;
                }

                if ($request->hasFile('adhar_card_front')) {
                    $file = $request->file('adhar_card_front');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->adhar_card_front = $filename;
                }

                if ($request->hasFile('adhar_card_back')) {
                    $file = $request->file('adhar_card_back');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->adhar_card_back = $filename;
                }

                $documents->save();

                return response()->json(['status' => true, 'message' => 'Basic Details stored successfully', 'data' => ['documents_details' => $documents]], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
    public function get_documents(Request $request)
    {
        try {
            $data  = DocumentUpload::where('user_id', $request->id)->first();
            if ($data) {
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}
