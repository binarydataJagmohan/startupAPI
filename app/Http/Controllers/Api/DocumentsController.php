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
use App\Models\DocumentsUpload;
use Illuminate\Validation\Rule;
use Mail;
use Illuminate\Support\Str;
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
                // 'proof_img' => [
                //     Rule::requiredIf(function () use ($request) {
                //         // Check if proof_img is already present in the database
                //         $existingProofImg = Documents::where('user_id', $request->user_id)
                //             ->whereNotNull('proof_img')
                //             ->first();

                //         return !$existingProofImg;
                //     }),
                //     'file',
                //     'mimetypes:application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                //     'max:20480', // Adjust the file size limit if needed
                // ],
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
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);
                    $data->proof_img = $filename;
                }

                $data->save();
                return response()->json(['status' => true, 'message' => 'Profile has been updated successfully', 'data' => ['data' => $data]], 200);
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
                return response()->json(['status' => true, 'message' => 'Profile has been updated successfully', 'data' => ['documents_details' => $data]], 200);
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
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function upload_documents(Request $request)
    {

        try {


            $user_id = $request->user_id;
            $user = User::findOrFail($user_id);
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
                if ($request->hasFile('certificate_incorporation')) {
                    $file = $request->file('certificate_incorporation');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->certificate_incorporation = $filename;
                }
                if ($request->hasFile('bank_statement_three_years')) {
                    $file = $request->file('bank_statement_three_years');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->bank_statement_three_years = $filename;
                }
                if ($request->hasFile('moa')) {
                    $file = $request->file('moa');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->moa = $filename;
                }
                if ($request->hasFile('aoa')) {
                    $file = $request->file('aoa');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->aoa = $filename;
                }

                $documents->save();

                return response()->json(['status' => true, 'message' => 'Profile has been updated successfully', 'data' => ['documents_details' => $documents]], 200);
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
                if ($request->hasFile('certificate_incorporation')) {
                    $file = $request->file('certificate_incorporation');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->certificate_incorporation = $filename;
                }
                if ($request->hasFile('bank_statement_three_years')) {
                    $file = $request->file('bank_statement_three_years');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->bank_statement_three_years = $filename;
                }
                if ($request->hasFile('moa')) {
                    $file = $request->file('moa');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->moa = $filename;
                }
                if ($request->hasFile('aoa')) {
                    $file = $request->file('aoa');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $documents->aoa = $filename;
                }
                $documents->save();
                $otp = VerificationCode::where('user_id', $user->id)->where('otp_type', 'email')->first();
                if ($otp) {
                    $otp->otp = rand(1000, 9999);
                    $otp->expire_at = Carbon::now()->addMinutes(1);
                    $otp->save();
                } else {
                    $otp = VerificationCode::create([
                        'user_id' => $user->id,
                        'otp' => rand(1000, 9999),
                        'otp_type' => 'email',
                        'expire_at' => Carbon::now()->addMinutes(1),
                    ]);
                }
                $mailtoken = Str::random(40);
                $domain = env('NEXT_URL_LOGIN');
                $url = $domain . '/?token=' . $mailtoken;
                $mail['url'] = $url;
                $mail['email'] = $user->email;
                $mail['name'] = $user->name;
                $mail['otp'] = $otp->otp;
                $mail['title'] = "Verify Your Account";
                $mail['body'] = "Please click on below link to verify your Account";
                $user->where('id', $user->id)->update(['email_verification_token' => $mailtoken, 'email_verified_at' => Carbon::now()]);
                Mail::send('email.emailVerify', ['mail' => $mail], function ($message) use ($mail) {
                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    $message->to($mail['email'])->subject($mail['title']);
                });
                return response()->json(['status' => true, 'message' => 'Profile has been updated successfully', 'data' => ['documents_details' => $documents]], 200);
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

    public function SelectedOptionsDocumentUpload(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $user = User::findOrFail($user_id);
            $checkuserid = DocumentsUpload::where('user_id', $request->user_id)->count();

            if ($checkuserid > 0) {
                $documents = DocumentsUpload::where('user_id', $user_id)->where('status', '1')->first();

                if ($request->hasFile('proof_of_network')) {
                    $file = $request->file('proof_of_network');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);

                    $Input = [
                        'filename' => $filename,
                        'filepath' => $filepath,
                        'type' => 'proof_network',
                    ];
                    DocumentsUpload::where('id', $documents->id)->update($Input);
                }
                if ($request->hasFile('proof_of_income')) {
                    $file = $request->file('proof_of_income');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);

                    $Input = [
                        'filename' => $filename,
                        'filepath' => $filepath,
                        'type' => 'proof_income',
                    ];
                    DocumentsUpload::where('id', $documents->id)->update($Input);
                }
                if ($request->hasFile('certificate_of_incorporation')) {
                    $file = $request->file('certificate_of_incorporation');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);

                    $Input = [
                        'filename' => $filename,
                        'filepath' => $filepath,
                        'type' => 'certificate_incorporation',
                    ];
                    DocumentsUpload::where('id', $documents->id)->update($Input);
                }
                if ($request->hasFile('ca_signed_net_angeable_2_crore')) {
                    $file = $request->file('ca_signed_net_angeable_2_crore');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);

                    $Input = [
                        'filename' => $filename,
                        'filepath' => $filepath,
                        'type' => 'ca_signed_angeable_2_crore',
                    ];
                    DocumentsUpload::where('id', $documents->id)->update($Input);
                }
                if ($request->hasFile('net_worth_atleast_10_crore')) {
                    $file = $request->file('net_worth_atleast_10_crore');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);

                    $Input = [
                        'filename' => $filename,
                        'filepath' => $filepath,
                        'type' => 'net_worth_10_crore',
                    ];
                    DocumentsUpload::where('id', $documents->id)->update($Input);
                }
                if ($request->hasFile('bank_statement_3_years')) {
                    $file = $request->file('bank_statement_3_years');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);

                    $Input = [
                        'filename' => $filename,
                        'filepath' => $filepath,
                        'type' => 'bank_statement_3_years',
                    ];
                    DocumentsUpload::where('id', $documents->id)->update($Input);
                }
                if ($request->hasFile('incorporation_certificate')) {
                    $file = $request->file('incorporation_certificate');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs');
                    $file->move($filepath, $filename);

                    $Input = [
                        'filename' => $filename,
                        'filepath' => $filepath,
                        'type' => 'incorporation_certificate',
                    ];
                    DocumentsUpload::where('id', $documents->id)->update($Input);
                }
                return response()->json(['status' => true, 'message' => 'Documents updated successfully', 'data' => ''], 200);
            } else {
                if ($request->hasFile('proof_of_network')) {
                    $file = $request->file('proof_of_network');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $Input = [];
                    $Input['user_id'] = $request->user_id;
                    $Input['filename'] = $filename;
                    $Input['filepath'] = $filepath;
                    $Input['type'] = 'proof_network';
                    $save_document_data = DocumentsUpload::insert($Input);
                }

                if ($request->hasFile('proof_of_income')) {
                    $file = $request->file('proof_of_income');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $Input = [];
                    $Input['user_id'] = $request->user_id;
                    $Input['filename'] = $filename;
                    $Input['filepath'] = $filepath;
                    $Input['type'] = 'proof_income';
                    $save_document_data = DocumentsUpload::insert($Input);
                }

                if ($request->hasFile('certificate_of_incorporation')) {
                    $file = $request->file('certificate_of_incorporation');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $Input = [];
                    $Input['user_id'] = $request->user_id;
                    $Input['filename'] = $filename;
                    $Input['filepath'] = $filepath;
                    $Input['type'] = 'certificate_incorporation';
                    $save_document_data = DocumentsUpload::insert($Input);
                }

                if ($request->hasFile('ca_signed_net_angeable_2_crore')) {
                    $file = $request->file('ca_signed_net_angeable_2_crore');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $Input = [];
                    $Input['user_id'] = $request->user_id;
                    $Input['filename'] = $filename;
                    $Input['filepath'] = $filepath;
                    $Input['type'] = 'ca_signed_angeable_2_crore';
                    $save_document_data = DocumentsUpload::insert($Input);
                }

                if ($request->hasFile('net_worth_atleast_10_crore')) {
                    $file = $request->file('net_worth_atleast_10_crore');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $Input = [];
                    $Input['user_id'] = $request->user_id;
                    $Input['filename'] = $filename;
                    $Input['filepath'] = $filepath;
                    $Input['type'] = 'net_worth_10_crore';
                    $save_document_data = DocumentsUpload::insert($Input);
                }

                if ($request->hasFile('bank_statement_3_years')) {
                    $file = $request->file('bank_statement_3_years');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $Input = [];
                    $Input['user_id'] = $request->user_id;
                    $Input['filename'] = $filename;
                    $Input['filepath'] = $filepath;
                    $Input['type'] = 'bank_statement_3_years';
                    $save_document_data = DocumentsUpload::insert($Input);
                }

                if ($request->hasFile('incorporation_certificate')) {
                    $file = $request->file('incorporation_certificate');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = public_path('docs/');
                    $file->move($filepath, $filename);
                    $Input = [];
                    $Input['user_id'] = $request->user_id;
                    $Input['filename'] = $filename;
                    $Input['filepath'] = $filepath;
                    $Input['type'] = 'incorporation_certificate';
                    $save_document_data = DocumentsUpload::insert($Input);
                }
                $documents = DocumentsUpload::where('user_id', $user_id)->where('status', '1')->first();
                //$documents->save();
                $otp = VerificationCode::where('user_id', $user->id)->where('otp_type', 'email')->first();
                if ($otp) {
                    $otp->otp = rand(1000, 9999);
                    $otp->expire_at = Carbon::now()->addMinutes(1);
                    $otp->save();
                } else {
                    $otp = VerificationCode::create([
                        'user_id' => $user->id,
                        'otp' => rand(1000, 9999),
                        'otp_type' => 'email',
                        'expire_at' => Carbon::now()->addMinutes(1),
                    ]);
                }
                $mailtoken = Str::random(40);
                $domain = env('NEXT_URL_LOGIN');
                $url = $domain . '/?token=' . $mailtoken;
                $mail['url'] = $url;
                $mail['name'] = $user->name;
                $mail['email'] = $user->email;
                $mail['otp'] = $otp->otp;
                $mail['title'] = "Verify Your Account";
                $mail['body'] = "Please click on below link to verify your Account";
                $user->where('id', $user->id)->update(['email_verification_token' => $mailtoken, 'email_verified_at' => Carbon::now()]);
                Mail::send('email.emailVerify', ['mail' => $mail], function ($message) use ($mail) {
                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    $message->to($mail['email'])->subject($mail['title']);
                });
                return response()->json(['status' => true, 'message' => 'Documents stored successfully', 'data' => ''], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }


    public function getUploadDocumentsByDocumentType(Request $request)
    {
        try {
            if ($request->document_type) {
                $data  = DocumentsUpload::where('user_id', $request->user_id)->where('type', $request->document_type)->first();
            } else {
                $data  = DocumentsUpload::where('user_id', $request->user_id)->get();
            }

            if ($data) {
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}
