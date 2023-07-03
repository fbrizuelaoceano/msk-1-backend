<?php

namespace App\Http\Controllers\Webhooks;

use App\Services\ZohoCRMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RebillController extends Controller
{
    private $zohoService;

    public function __construct(ZohoCRMService $service)
    {
        $this->zohoService = $service;
    }

    public function newPayment(Request $request)
    {
        $jsonPayload = file_get_contents('php://input');
        $data = json_decode($jsonPayload, true);

        Log::info("newPayment: " . print_r($data, true));

        $id = $data['payment']['id'];
        $email = $data['payment']['customer']['email'];
        $status = $data['payment']['status'];

        $paymentLink = DB::connection('omApiPayments')->select("SELECT * FROM rebill_customers AS rebill_c INNER JOIN payment_links AS pay_l ON rebill_c.id = pay_l.rebill_customer_id WHERE rebill_c.email = :email ORDER BY rebill_c.created_at DESC LIMIT 1;", ["email" => $email]);

        Log::info("paymentLink get by email: " . print_r($paymentLink, true));

        $setPaymentLink = $paymentLink[0];

        $statusPaymentLink = [
            ["PENDING" => "pending"],
            ["SUCCEEDED" => "Contrato Efectivo"],
            ["FAILED" => "Pago Rechazado"]
        ];
        $setPaymentLink->status = $statusPaymentLink[$status];

        $token = env('APP_DEBUG') ? env('REBILL_TOKEN_PRD') : env('REBILL_TOKEN_PRD');


        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get('https://api.rebill.to/v2/payments/' . $id)->json();

        Log::info("response rebill newPayment: " . print_r($response, true));





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
        $dataWebhook = json_decode($jsonPayload, true);

        Log::channel('wh')->info("changeStatusPayment: " . print_r($dataWebhook, true));

        $idWebhook = $dataWebhook['payment']['id'];
        $prevStatusWebhook = $dataWebhook['payment']['previousStatus'];
        $newStatusWebhook = $dataWebhook['payment']['newStatus'];

        $token = env('APP_DEBUG') ? env('REBILL_TOKEN_PRD') : env('REBILL_TOKEN_PRD');

        $responsePaymentById = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get('https://api.rebill.to/v2/payments/' . $idWebhook)->json();

        Log::channel('wh')->info("responsePaymentById getPaymentById,changeStatusPayment: " . print_r($responsePaymentById, true));

        $emailPaymentById = $responsePaymentById['payment']['customer']['email'];
        $paymentLink_Api_Payments = DB::connection('omApiPayments')->select("SELECT * FROM rebill_customers AS rebill_c INNER JOIN payment_links AS pay_l ON rebill_c.id = pay_l.rebill_customer_id WHERE rebill_c.email = :email ORDER BY rebill_c.created_at DESC LIMIT 1;", ["email" => $emailPaymentById]);

        Log::channel('wh')->info("paymentLink getByemail,changeStatusPayment: " . print_r($paymentLink_Api_Payments, true));

        $setPaymentLink_Api_Payments = $paymentLink_Api_Payments[0];

        $mapping_Status = [
            ["PENDING" => "pending"],
            ["SUCCEEDED" => "Contrato Efectivo"],
            ["FAILED" => "Pago Rechazado"]
        ];
        $setPaymentLink_Api_Payments->status = $mapping_Status[$newStatusWebhook];

        $setPaymentLink_Api_Payments->save();

    }

    public function test()
    {


        $contract = $this->zohoService->getByEntityId("Sales_Orders", "5344455000004398120");
        $token = env('REBILL_TOKEN_DEV');

        $responsePaymentById = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get('https://api.rebill.to/v2/payments/207a57ae-9dfb-4518-8efa-a1bdbb5cd337')->json();


        $responseSuscriptionById = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get('https://api.rebill.to/v2/subscriptions/' . $responsePaymentById['billingSchedulesId'][0])->json();

        return response()->json([
            "wh" => $responsePaymentById,
            "sale" => $contract
        ]);

    }

}