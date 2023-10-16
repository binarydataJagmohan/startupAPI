<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payments;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Session;
use Stripe\Customer;
use Stripe\Token;
use Stripe\Charge;
use Stripe\Exception\CardException;
use Illuminate\Http\Exceptions\HttpException;
use Carbon\Carbon;
use Stripe\PaymentIntent;
use App\Models\User;
use App\Models\Business;
use Mail;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function savePayment(Request $request)
    {

        try {

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $request[1]['subscription_value'], // amount in cents
                'currency' => 'usd',
                'payment_method' => $request[0]['id'],
                'confirm' => true,
            ]);

            $data = new Payments();
            $data->user_id =  $request[1]['user_id'];
            $data->business_id = $request[2]['id'];
            $data->payment_id = $request[0]['id'];
            $data->amount = $request[1]['repayment_value'];
            $data->card_number = $request[0]['card']['last4'];
            $data->zip_code = $request[0]['billing_details']['address']['postal_code'];
            $data->expiry_date = $request[0]['card']['exp_month'] . '/' . $request[0]['card']['exp_year'];
            $data->status = "success";
            $data->save();
            return response()->json(['status' => true, 'message' => 'Payment successful', 'data' => $data, 'paymentIntent' => $paymentIntent], 200);
            $user = User::where('id', $request[1]['user_id'])->first();
            $business = Business::where('id', $request[2]['id'])->first();
            $mail['username'] = $user->name;
            $mail['email'] = $user->email;
            $mail['user'] = $user;
            $mail['booking'] = $request[1];
            $mail['businessUnit'] = $request[2];
            $mail['business'] = $business;
            $mail['title'] = "Payment Success";
            $mail['body'] = "Your Payment has done successfully. ";
            $timestamp = Carbon::parse($mail['booking']['repayment_date']);
            $mail['date'] = $timestamp->format('Y-m-d');
    
            Mail::send('email.paymentSuccess', ['mail' => $mail], function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['title']);
            });
    
    
    
            $startup = User::find($business->user_id);
    
            $mail1Data = [
                'startup' => $startup,
                'email' => $startup->email,
                'user' => $user,
                'booking' => $request[1],
                'businessUnit' => $request[2],
                'business' => $business,
                'title' => "Fund Occupied",
                'body' => "Your raised fund has been occupied successfully.",
                'date' => Carbon::parse($request[1]['repayment_date'])->format('Y-m-d'),
            ];
        
            Mail::send('email.StartupFundNotification', ['mail' => $mail1Data], function ($message) use ($mail1Data) {
                $message->to($mail1Data['email'])->subject($mail1Data['title']);
            });



          
        } catch (CardException $e) {
            // Card error occurred
            $body = $e->getJsonBody();
            $error = $body['error'];

            return response()->json(['status' => false, 'message' => $error['message']], 200);
        } catch (\Exception $e) {throw new HttpException(500, $e->getMessage());
            // Other error occurred
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
