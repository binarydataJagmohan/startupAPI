<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ErrorLogController extends Controller
{
    public function getErrorLog()
    {

        try {
            $data = ErrorLog::select('error_logs.*')
                ->get();
            if ($data) {
                return response()->json(['status' => true, 'message' => "Data fetching successfully", 'data' => $data], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteerrorlog(Request $request)
    {
        try {
            $id = $request->id;


            $errorlog = ErrorLog::find($id);

            if (!$errorlog) {
                return response()->json(['message' => 'Errorlog not found'], 404);
            }

            $error_message = $errorlog->error_message;
            $line_number = $errorlog->line_number;
            $file_name = $errorlog->file_name;

            // Delete rows where the data matches
            ErrorLog::Where(function ($query) use ($error_message, $line_number, $file_name) {
                $query->where('error_message', $error_message)
                    ->where('line_number', $line_number)
                    ->where('file_name', $file_name);
            })
                ->delete();

            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully',
                'data' => $id

            ]);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function deleteAllErorlog()
    {
        try {
            ErrorLog::truncate();

            return response()->json([
                'status' => true,
                'message' => 'All Error Logs deleted successfully',
                'data' => ''
            ]);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());

        }
    }
}
