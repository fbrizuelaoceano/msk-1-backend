<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Product;
use App\Models\User;
use App\Models\Contact;
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

        $contactObj = json_decode($_POST['contact']);
        $saleObj = json_decode($_POST['sale']);

        Log::info(print_r($contactObj));
        Log::info(print_r($saleObj));

        dd($contactObj->Usuario);

        $user = User::updateOrCreate(['email' => $contactObj->Usuario], [
            'name' => $saleObj->Full_Name,
            'email' => $saleObj->Usuario,
            'password' => Hash::make($saleObj->Password),
        ]);

        $contact = Contact::updateOrCreate(['email' => $saleObj->Usuario], [
            'last_name' => $contactObj->Last_Name,
            'email' => $saleObj->Usuario,
            'user_id' => $user->id,
            'entity_id_crm' => $contactObj->id
        ]);

        Contract::updateOrCreate(['entity_id_crm' => $saleObj->id], [
            'entity_id_crm' => $saleObj->id,
            'country' => $contactObj->Pais,
            'currency' => $contactObj->Pais,
        ]);

        $productDetails = $saleObj->Product_Details;

        foreach ($productDetails as $pd) {
            Product::updateOrCreate([
                'entity_id_crm' => $pd->id,
                'contract_id' => $saleObj->id
            ], [
                    'entity_id_crm' => $pd->id,
                    'contract_id' => $saleObj->id,
                    'quantity' => $pd->quantity,
                    'discount' => $pd->Discount,
                    'price' => $pd->total,
                    'product_code' => (int) $pd->product->Product_Code
                ]);
        }


        return response()->json(['user' => $user, 'contact' => $contact]);
    }
}