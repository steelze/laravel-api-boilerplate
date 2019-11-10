<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Response;

class ResponseHelper
{
    /**
     * Returns success json response
     *
     * @param  String  $message
     * @param  Mixed  $payload
     * @param  Int  $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    static public function withSuccess(String $message = '', $payload = [], Int $status_code = Response::HTTP_OK)
    {
        return response()->json(['status' => 'success', 'message' => $message, 'payload' => $payload], $status_code);
    }

    /**
     * Returns error json response
     *
     * @param  String  $message
     * @param  Mixed  $payload
     * @param  Int  $status_code
     * @param  Array  $headers
     * @return \Illuminate\Http\JsonResponse
     */
    static public function withError(String $message = '', $payload = [], Int $status_code = Response::HTTP_INTERNAL_SERVER_ERROR, $headers = [])
    {
        return response()->json(['status' => 'error', 'message' => $message, 'payload' => $payload], $status_code)->withHeaders($headers);
    }
}
