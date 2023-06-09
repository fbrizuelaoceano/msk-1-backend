<?php

namespace App\Http\Controllers\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
class RebillController extends Controller
{
    public function newPayment(Request $request)
    {
        $jsonPayload = file_get_contents('php://input');
        $data = json_decode($jsonPayload, true);

        Log::info("newPayment: " . print_r($data, true));

        $id = $data['payment']['id'];
        $email = $data['payment']['customer']['email'];
        $status = $data['payment']['status'];
        // $email = "brizuelafacundoignacio@gmail.com";
        $paymentLink = DB::connection('omApiPayments')->select("SELECT * FROM rebill_customers AS rebill_c INNER JOIN payment_links AS pay_l ON rebill_c.id = pay_l.rebill_customer_id WHERE rebill_c.email = :email ORDER BY rebill_c.created_at DESC LIMIT 1;", ["email" => $email]);

        Log::info("paymentLink get by email: " . print_r($paymentLink , true));

        $setPaymentLink = $paymentLink[0];
        
        $statusPaymentLink = [
            ["PENDING" => "pending"],
            ["SUCCEEDED" => "Contrato Efectivo"],
            ["FAILED" => "Pago Rechazado"]
        ];
        $setPaymentLink->status = $statusPaymentLink[$status];
        $setPaymentLink->save();

        // $token = "API_KEY_955a1b47-1b02-4f09-af6b-5be66da4d8d4";        

        // //->apipaymentlink.->update($newStatus)->where("",$)

        // $response = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token
        // ])->get('https://api.rebill.to/v2/payments/'.$id)->json();
        
        // Log::info("response getPaymentById: " . print_r($response, true));

        // if ($response->failed()) {
        //     Log::info("Error, Response, getPayMentByID, changeStatusPayment: " . print_r($response, true));
        //     // echo "HTTP Error: " . $response->status();
        // } else {
        //     // echo $response->body();
        //     // Log::info("bodyPayment, Response, getPayMentByID, changeStatusPayment: " . print_r($response, true));


        // }

    }

    public function newSubscription(Request $request)
    {
        $jsonPayload = file_get_contents('php://input');
        $data = json_decode($jsonPayload, true);

        Log::info("newSubscription: " . print_r($data, true));
    }

    public function changeStatusPayment(Request $request)
    {
        $jsonPayload = file_get_contents('php://input');
        $data = json_decode($jsonPayload, true);

        Log::info("changeStatusPayment: " . print_r($data, true));

        

        $id = $data['payment']['id'];


        $token = "API_KEY_955a1b47-1b02-4f09-af6b-5be66da4d8d4";

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->get('https://api.rebill.to/v2/payments/'.$id)->json();
        
        Log::info("response getPaymentById: " . print_r($response, true));

        if ($response->failed()) {
            Log::info("Error, Response, getPayMentByID, changeStatusPayment: " . print_r($response, true));
            // echo "HTTP Error: " . $response->status();
        } else {
            // echo $response->body();
            // Log::info("bodyPayment, Response, getPayMentByID, changeStatusPayment: " . print_r($response, true));


        }

    }
}
