<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponseTrait
{

    /**
     * Generate a JSON response for successful operations.
     *
     * @param  mixed  $data
     * @param  string  $messageKey
     * @param  int  $statusCode
     * @return JsonResponse
     */
    public function successResponse($data, $messageKey = 'success', $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => trans($messageKey),
            "message_code"=> $messageKey,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Generate a JSON response for errors.
     *
     * @param  string  $message
     * @param  string  $messageKey
     * @param  int  $statusCode
     * @return JsonResponse
     */
    public function errorResponse( $messageKey = 'error', $statusCode = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => trans($messageKey),
            "message_code"=> $messageKey,
        ], $statusCode);
    }
}
