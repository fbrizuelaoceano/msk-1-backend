<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function ContactRegister(Request $request)
    {
        return response()->json($request->all());
    }

    public function ContactDetails(Request $request)
    {

    }

    public function ContactUpdate(Request $request)
    {

    }

    public function ContractRegister(Request $request)
    {

    }
}