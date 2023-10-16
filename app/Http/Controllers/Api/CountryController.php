<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CountryController extends Controller
{
    public function all_countries()
    {
        try {
            $countries = Country::get();
            if ($countries) {
                return response()->json(['status' => true, 'message' => 'all countries', 'error' => '', 'data' => $countries], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'no countries found!', 'error' => '', 'data' => ''], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function single_country($id)
    {
        try {
            if ($id) {
                $country = Country::find($id);
                if ($country) {
                    return response()->json(['status' => true, 'message' => 'country data found', 'error' => '', 'data' => $country], 200);
                } else {
                    return response()->json(['status' => false, 'message' => 'country not found!', 'error' => '', 'data' => ''], 200);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'Invalid request!', 'error' => '', 'data' => ''], 200);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}
