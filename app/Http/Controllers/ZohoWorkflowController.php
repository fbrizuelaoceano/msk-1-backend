<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Product;
use App\Models\Profession;
use App\Models\Speciality;
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

        Log::info(print_r($contactObj, true));
        Log::info(print_r($saleObj, true));

        dd($contactObj->Usuario);

        $profession = Profession::where('name', $contactObj->Profesi_n)->first();
        $specialty = Speciality::where('name', $contactObj->Especialidad)->first();

        $user = User::updateOrCreate(['email' => $contactObj->Usuario], [
            'name' => $contactObj->Full_Name,
            'email' => $contactObj->Usuario,
            'password' => Hash::make($contactObj->Password),
        ]);

        $contact = Contact::updateOrCreate(['entity_id_crm' => $contactObj->Usuario], [
            'name' => $contactObj->First_Name,
            'last_name' => $contactObj->Last_Name,
            'email' => $contactObj->Usuario,
            'profession' => $profession->id,
            'specialty' => $specialty->id,
            'user_id' => $user->id,
            'entity_id_crm' => $contactObj->id,
            'rfc' => $contactObj->RFC,
            'sex' => $contactObj->Sexo,
            'country' => $contactObj->Pais,
            'phone' => $contactObj->Phone,
            'validate' => $contactObj->Validador,
            'fiscal_regime' => $contactObj->R_gimen_fiscal,
            'postal_code' => $contactObj->Mailing_Zip,
            'address' => $contactObj->Mailing_Street,
            'date_of_birth' => $contactObj->Date_of_Birth
        ]);

        $contract = Contract::updateOrCreate(['entity_id_crm' => $saleObj->id], [
            'contact_id' => $contact->id,
            'entity_id_crm' => $saleObj->id,
            'so_crm' => $saleObj->SO_Number,
            'status' => $saleObj->Status,
            'status_payment' => $saleObj->Estado_de_cobro,
            'country' => $contactObj->Pais,
            'currency' => $contactObj->Currency,
        ]);

        $productDetails = $saleObj->Product_Details;

        foreach ($productDetails as $pd) {
            Product::updateOrCreate([
                'entity_id_crm' => $pd->id,
                'contract_entity_id' => $saleObj->id
            ], [
                    'entity_id_crm' => $pd->id,
                    'contract_id' => $contract->id,
                    'contract_entity_id' => $saleObj->id,
                    'quantity' => $pd->quantity,
                    'discount' => $pd->Discount,
                    'price' => $pd->total,
                    'product_code' => (int) $pd->product->Product_Code
                ]);
        }


        return response()->json(['user' => $user, 'contact' => $contact]);
    }

    public function ValidatedUser(Request $request)
    {
        $contactObj = json_decode($_POST['contact']);

        /* Log::info(print_r($contactObj, true));
        Log::info(print_r($saleObj, true)); */

        //dd($contactObj->Usuario);

        $contact = Contact::updateOrCreate(['email' => $contactObj->Usuario], [
            'validate' => $contactObj->Validador,
        ]);

        return response()->json(['contact' => $contact]);
    }
}