<?php

namespace App\Http\Controllers\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class RebillController extends Controller
{
    public function newPayment(Request $request)
    {
        $content = $request->getContent();

        Log::info("newPayment: " . print_r($content));
    }

    public function changeStatusPayment(Request $request)
    {
        $content = $request->getContent();
        Log::info("changeStatusPayment: " . print_r($content));
    }
}