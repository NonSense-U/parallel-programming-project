<?php
namespace App\Helpers;

class ApiResponse
{
    public static function success($message = 'Success', $data = null, $statusCode = 200)
    {
        $response = [
            'status' => 'success',
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    public static function fail($message = 'Error', $errors = null, $statusCode = 500)
    {
        $response = [
            'status' => 'fail',
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
