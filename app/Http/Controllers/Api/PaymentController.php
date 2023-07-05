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
use App\Models\User;
use Illuminate\Http\Exceptions\HttpException;
use Carbon\Carbon;

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
           

            $customer = Customer::create([
              'name' => 'kamal',
              'email' => 'customer@email.com'
            ]);

            
            // Create a token for the payment source
            $token = Token::create([
              'card' => [
                'name' => request('cardholder_name'),
                'number' => request('card_number'),
                'exp_month' => request('exp_month'),
                'exp_year' => request('exp_year'),
                'cvc' => request('cvc')
              ]
            ]);
            $customer->sources->create(['source' => $token->id]);
            $charge = Charge::create([
              'amount' => 1000,
              'currency' => 'usd',
              'customer' => $customer->id,
              'source' => $customer->default_source
            ]);

            
            return response()->json(['status' => true, 'message' => 'Payment successful'], 200);
        } catch (CardException $e) {
            // Card error occurred
            $body = $e->getJsonBody();
            $error = $body['error'];

            return response()->json(['status' => false, 'message' => $error['message']], 422);
        } catch (\Exception $e) {
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
