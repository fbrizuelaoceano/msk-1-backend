<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Product;
use App\Models\Profession;
use App\Models\Speciality;
use App\Models\Quote;
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

        /*  Log::info(print_r($contactObj, true));
         Log::info(print_r($saleObj, true));
  */
        //dd($contactObj->Usuario);

        $user = User::updateOrCreate(['email' => $contactObj->Usuario], [
            'name' => $contactObj->Full_Name,
            'email' => $contactObj->Usuario,
            'password' => Hash::make($contactObj->Password),
        ]);

        $contact = Contact::updateOrCreate(['entity_id_crm' => $contactObj->id], [
            'name' => $contactObj->First_Name,
            'last_name' => $contactObj->Last_Name,
            'email' => $contactObj->Usuario,
            'profession' => $contactObj->Profesi_n,
            'specialty' => $contactObj->Especialidad,
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
            'country' => $saleObj->Pais_de_facturaci_n,
            'currency' => $saleObj->Currency,
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
    
    function UpdateQuotes(Request $request){
        $quoteObj = json_decode($_POST['quote']);
        Log::info("quoteObj: " . print_r($quoteObj, true));
        
        // $quoteObj = $request->quote;

        $mskObjDBQuote = [
            'entity_id_crm' => $quoteObj->id,
            'Discount' => $quoteObj->Discount,
            'currency_symbol' => $quoteObj['$currency_symbol'],
            'field_states' => $quoteObj['$field_states'],
            'Seleccione_total_de_pagos_recurrentes' => $quoteObj->Seleccione_total_de_pagos_recurrentes,
            'M_todo_de_pago' => $quoteObj->M_todo_de_pago,
            'Currency' => $quoteObj->Currency,
            'otro_so' => $quoteObj->otro_so,
            'Modo_de_pago' => $quoteObj->Modo_de_pago,
            'Quote_Stage' => $quoteObj->Quote_Stage,
            'Grand_Total' => $quoteObj->Grand_Total,
            'Modified_Time' => $quoteObj->Modified_Time,
            'Sub_Total' => $quoteObj->Sub_Total,
            'Subject' => $quoteObj->Subject,
            'Quote_Number' => $quoteObj->Quote_Number,
        ];
        Log::info("mskObjDBQuote: " . print_r($mskObjDBQuote, true));

        $quote = Quote::updateOrCreate(
            [
                'entity_id_crm' => $mskObjDBQuote['entity_id_crm']
            ],
            $mskObjDBQuote
        );        
        Log::info("Quote::updateOrCreate: " . print_r($quote, true));
       
        // dd($mskObjDBQuote);

        return response()->json(
            $mskObjDBQuote,
            $quote
        );
    }
   
}

