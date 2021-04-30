<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected function responseOk($body, $message = '', $status = 200, $headers = [], $options = 0)
    {
        $content = [
            'status' => true,
            'code' => $status,
            'body' => $body,
            'message' => $message
        ];

        return response()->json($content, $status, $headers, $options);
    }

    protected function responseError($body, $message = '', $status = 400, $headers = [], $options = 0)
    {
        $content = [
            'status' => false,
            'code' => $status,
            'body' => $body,
            'message' => $message
        ];

        return response()->json($content, $status, $headers, $options);
    }
}
