<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\InvestorBooking;
use Illuminate\Http\Request;
use App\Models\BusinessUnit;
use App\Models\User;
use App\Models\Business;
use Mail;
use Carbon\Carbon;

class InvestorBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function booking(Request $request)
    {
        $booking = new InvestorBooking();
        $booking->user_id = $request->user_id;
        $booking->business_id = $request->business_id;
        $booking->repayment_date = $request->repay_date;
        $booking->repayment_value = $request->repayment_value;
        $booking->no_of_units = $request->no_of_units;
        $booking->subscription_value = $request->subscription_value;

        // Check if the requested number of units is valid
        $businessUnit = BusinessUnit::where('business_id', $request->business_id)->first();
        if ($request->no_of_units > $businessUnit->total_units) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid number of units. Not enough units available.'
            ]);
        }

        // Update the units in the business_units table
        $businessUnit->no_of_units -= $request->no_of_units;
        $businessUnit->save();

        $booking->save();

        $user = User::where('id', $request->user_id)->first();
        $business = Business::where('id', $request->business_id)->first();
        $mail['username'] = $user->name;
        $mail['email'] = $user->email;
        $mail['user'] = $user;
        $mail['booking'] = $booking;
        $mail['businessUnit'] = $businessUnit;
        $mail['business'] = $business;
        $mail['title'] = "Payment Success";
        $mail['body'] = "Your Payment has done successfully. ";
        $timestamp = Carbon::parse($mail['booking']->repayment_date);
        $mail['date'] = $timestamp->format('Y-m-d');

        Mail::send('email.paymentSuccess', ['mail' => $mail], function ($message) use ($mail) {
            $message->to($mail['email'])->subject($mail['title']);
        });



        $startup = User::find($business->user_id);

        $mail1Data = [
            'startup' => $startup,
            'email' => $startup->email,
            'user' => $user,
            'booking' => $booking,
            'businessUnit' => $businessUnit,
            'business' => $business,
            'title' => "Fund Occupied",
            'body' => "Your raised fund has been occupied successfully.",
            'date' => Carbon::parse($booking->repayment_date)->format('Y-m-d'),
        ];
    
        Mail::send('email.StartupFundNotification', ['mail' => $mail1Data], function ($message) use ($mail1Data) {
            $message->to($mail1Data['email'])->subject($mail1Data['title']);
        });

        return response()->json([
            'status' => true,
            'data' => $businessUnit,
            'message' => 'Data Inserted Successfully'
        ]);
    }





    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InvestorBooking  $investorBooking
     * @return \Illuminate\Http\Response
     */
    public function show(InvestorBooking $investorBooking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InvestorBooking  $investorBooking
     * @return \Illuminate\Http\Response
     */
    public function edit(InvestorBooking $investorBooking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InvestorBooking  $investorBooking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InvestorBooking $investorBooking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InvestorBooking  $investorBooking
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvestorBooking $investorBooking)
    {
        //
    }
}
