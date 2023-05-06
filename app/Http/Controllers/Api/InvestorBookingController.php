<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\InvestorBooking;
use Illuminate\Http\Request;
use App\Models\BusinessUnit;

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
    $booking->repayment_date = $request->repayment_date;
    $booking->repayment_value = $request->repayment_value;
    $booking->no_of_units = $request->no_of_units;

    $booking->save();

    // Update the units in the business_units table
    $businessUnit = BusinessUnit::where('business_id', $request->business_id)->first();
    $businessUnit->no_of_units -= $request->no_of_units;
    $businessUnit->save();

    return response()->json([
        'status' => true,
        'data' => $booking,
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
