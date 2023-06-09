<?php

namespace App\Http\Controllers\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
class RebillController extends Controller
{
    public function newPayment(Request $request)
    {
        $jsonPayload = file_get_contents('php://input');
        $data = json_decode($jsonPayload, true);

        Log::info("newPayment: " . print_r($data, true));

        $apiPayments = DB::connection('omApiPayments')->select('SELECT * FROM payment_links');

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

        $exampleRequestWebhook = '{
            "payment": {
             "id": "024160d0-7ba4-48c0-828e-b2c6fab86e53",
             "createdAt": "2022-09-06T14:38:00.082Z",
             "newStatus": "REFUNDED",
             "orderId": "637285ca-85f5-476e-bade-40b6ec61da2c",
             "previousStatus": "SUCCEEDED"
            },
             "webhook":{
                "id":"bf8d33b1-2f0b-4efc-bc02-bf9d3a04a850",
                "event":"payment-change-status",
                "url":"https://your.domain/payment-change-status",
                "logId":"eeb168f-ed10-4619-9eb2-a48b8a6744e2"
             }
        }';

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
