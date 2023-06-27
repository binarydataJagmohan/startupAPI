<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\User;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOptions($id)
    {
        $options = Option::where('user_id', $id)->get();

        return response()->json([
            'status' => true,
            'message' => 'Options retrieved successfully',
            'data' => $options,
        ]);
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
    public function notificationConfigStore(Request $request)
    {
        try {
            $userId = $request->user_id;
            $optionName = $request->option_name;
            $optionValue = $request->option_value;
    
            $option = Option::updateOrCreate(
                ['user_id' => $userId,'option_name' => $optionName],
                ['option_value' => $optionValue]
            );
    
            if ($option) {
                return response()->json([
                    'status' => true,
                    'message' => 'Options saved successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Options not saved successfully'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error Occurred: ' . $e->getMessage()
            ], 500);
        }
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
