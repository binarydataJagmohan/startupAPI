<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestorBooking;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Session;
use Stripe\Customer;
use Stripe\Token;
use Stripe\Charge;
use Stripe\Exception\CardException;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpException;
use App\Models\Business;
use Carbon\Carbon;
use App\Models\BusinessUnit;
use App\Models\BankDetails;

class StripeController extends Controller
{
    
    public function savePayment(Request $request)
    {

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // try {
            $amount = $request->amount * 100;
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $token = \Stripe\Token::create([
                'card' => [
                    'name' => $request->name,
                    'number' => $request->card_number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc
                ],
            ]);
            $customer->sources->create(['source' => $token->id]);

            $charge = Charge::create([
                'amount' => $amount,
                'currency' => 'aed',
                'customer' => $customer->id,
                'source' => $customer->default_source
            ]);
            
            return response()->json(['status' => true, 'message' => 'Payment successful'], 200);
        // }  catch (\Exception $e) {
        //     // Other error occurred
        //     return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        // }
    }

}
