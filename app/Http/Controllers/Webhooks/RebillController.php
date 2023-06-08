<?php

namespace App\Http\Controllers\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class RebillController extends Controller
{
    public function newPayment(Request $request)
    {
        Log::info(print_r($request->all()));
    }

    public function changeStatusPayment(Request $request)
    {
        Log::info(print_r($request->all()));
    }
}