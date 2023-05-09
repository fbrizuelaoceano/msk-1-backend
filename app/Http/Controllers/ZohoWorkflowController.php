<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ZohoWorkflowController extends Controller
{
    public function setNewPasswordFromMSK(Request $request)
    {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required',
        ]);

        $user = User::where(["email" => $request->email])->first();
        $user->password = Hash::make($request->password);
        $user->save();


        return response()->json([
            'message' => 'Successfully find user!',
        ], 201);
    }

    public function salesForCRM(Request $request)
    {
        $contact = collect($_POST['contact'])->toArray()[0];
        $sale = collect($_POST['sale'])->toArray()[0];


        $contactObj = json_decode($contact)[0];
        $saleObj = json_decode($sale)[0];
        Log::info(print_r($contactObj, true));

        $user = User::updateOrCreate(['email' => $contactObj->Email], [
            'name' => $contactObj->First_Name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $contact = Contact::updateOrCreate(['email' => $contactObj->Email], [
            'last_name' => $contactObj->Last_Name,
            'email' => $contactObj->Email,
            'user_id' => $user->id,
            'entity_id_crm' => $contactObj->id
        ]);

        return response()->json(['user' => $user, 'contact' => $contact]);
    }
}