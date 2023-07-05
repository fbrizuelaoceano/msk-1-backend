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

        // Log::info("paymentLink get by email: " . print_r($paymentLink, true));

        $setPaymentLink = $paymentLink[0];

        $statusPaymentLink = [
            ["PENDING" => "pending"],
            ["SUCCEEDED" => "Contrato Efectivo"],
            ["FAILED" => "Pago Rechazado"]
        ];
        $setPaymentLink->status = $statusPaymentLink[$status];

        $token = env('APP_DEBUG') ? env('REBILL_TOKEN_PRD') : env('REBILL_TOKEN_PRD');

        if($status === "SUCCEEDED"){
            $this->payloadZohoCRMMSK($token,$id);
        }

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
    public function payloadZohoCRMMSK($apiToken,$idPayment){
    // public function payloadZohoCRMMSK(Request $request){
        Log::info("newPayment-payloadZohoCRMMSK-idPayment: " . print_r($idPayment, true));

        // $idPayment = "5cf7da17-3d17-4912-b45f-fa26da5a7e7b";
        // $apiToken = env('APP_DEBUG') ? env('REBILL_TOKEN_DEV') : env('REBILL_TOKEN_PRD');
        
        $responsePaymentById = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $apiToken
        ])->get('https://api.rebill.to/v2/payments/'.$idPayment)->json();
        $responseSuscriptionById = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $apiToken
        ])->get("https://api.rebill.to/v2/subscriptions/".$responsePaymentById["billingSchedulesId"][0])->json();

        $soNumber = isset($responseSuscriptionById["metadataObject"]["so_number"]) ? str_replace("x", "", $responseSuscriptionById["metadataObject"]["so_number"]) : "";
        $query = "Sales_Orders/search?criteria=(otro_so:equals:".$soNumber.")";
        $getSalesOrdersBySO_OM = $this->zohoService->get($query);
        $getSalesOrdersById = $this->zohoService->GetByIdAllDetails('Sales_Orders',$getSalesOrdersBySO_OM['data'][0]["id"]);
        Log::info("newPayment-payloadZohoCRMMSK-getSalesOrdersById: " . print_r($getSalesOrdersById, true));
        $paso5DetalleDePagos=$getSalesOrdersById['data'][0]["Paso_5_Detalle_pagos"];
        // *#region* Depuracion
        // $responsePaymentById["id"] = "2";
        // //limpiar campo 
        // $data = [
            // "data" => [
                // [
                    // "Paso_5_Detalle_pagos" => []
                // ]
            // ]
        // ];
        // $responseUdateSaleOrder = $this->zohoService->Update('Sales_Orders', $data, "5344455000004398120");
        // *#endregion*
        // *#region* Carga de data para actualizar cobros en crm zoho msk
        $data = [
            "data" => [
                [
                    "Paso_5_Detalle_pagos" => $paso5DetalleDePagos
                ]
            ]
        ];
        $fechaRecortada = date("Y-m-d", strtotime($responsePaymentById["createdAt"]));
                $paymentData = [
                    "Fecha_Cobro" => $fechaRecortada,
                    "Cobro_ID" => $responsePaymentById["id"],
                    "Numero_de_cobro" => count($paso5DetalleDePagos) < 1 ? 1 : count($paso5DetalleDePagos) + 1
                ];
        array_push($data["data"][0]["Paso_5_Detalle_pagos"],$paymentData);
        // *#endregion*
        Log::info("newPayment-payloadZohoCRMMSK-data: " . print_r($data, true));
        
        //Actualizacion de sale order en zrmzohomsk
        $responseUdateSaleOrder = $this->zohoService->Update('Sales_Orders', $data, $getSalesOrdersById['data'][0]["id"]);
        Log::info("newPayment-payloadZohoCRMMSK-responseUdateSaleOrder: " . print_r($responseUdateSaleOrder, true));
    }
    
    public function test()
    {
        // $contract = $this->zohoService->getByEntityId("Sales_Orders", "5344455000004398120");
        // $contact = $this->zohoService->getByEntityId("Contacts", "5344455000004398022");
        
        $apiKey = env('REBILL_TOKEN_DEV');

        $responsePaymentById = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey
        ])->get('https://api.rebill.to/v2/payments/5cf7da17-3d17-4912-b45f-fa26da5a7e7b')->json();

        $responseSuscriptionById = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey
        ])->get("https://api.rebill.to/v2/subscriptions/".$responsePaymentById["billingSchedulesId"][0])->json();
        
        $soNumber = isset($responseSuscriptionById["metadataObject"]["so_number"]) ? str_replace("x", "", $responseSuscriptionById["metadataObject"]["so_number"]) : "";
       
        // Hay dos formas de cargar los pagos a la tabla de cobros de crm.
        // 1.Buscando la suscripcion y los payments de la suscripcion. o 2.Buscando el saleorder y tomando los cobros anteriores y sumarle el ultimo.

        // Forma 1
        // forma1($responseSuscriptionById);

        // forma 2
        // $getSalesOrdersBySO_OM = $this->zohoService->get("SalesOrders/search?criteria=(otro_so:equals:".$soNumber.")");
        $query = "Sales_Orders/search?criteria=(otro_so:equals:".$soNumber.")";
        $getSalesOrdersBySO_OM = $this->zohoService->get($query);
        $getSalesOrdersById = $this->zohoService->GetByIdAllDetails('Sales_Orders',$getSalesOrdersBySO_OM['data'][0]["id"]);
        
        $paso5DetalleDePagos=$getSalesOrdersById['data'][0]["Paso_5_Detalle_pagos"];
        // *#region* Depuracion
        // $responsePaymentById["id"] = "2";
        // //limpiar campo 
        // $data = [
        //     "data" => [
        //         [
        //             "Paso_5_Detalle_pagos" => []
        //         ]
        //     ]
        // ];
        // $responseUdateSaleOrder = $this->zohoService->Update('Sales_Orders', $data, "5344455000004398120");
        // *#endregion*
        $data = [
            "data" => [
                [
                    "Paso_5_Detalle_pagos" => $paso5DetalleDePagos
                ]
            ]
        ];
        $fechaRecortada = date("Y-m-d", strtotime($responsePaymentById["createdAt"]));
                $paymentData = [
                    "Fecha_Cobro" => $fechaRecortada,
                    "Cobro_ID" => $responsePaymentById["id"],
                    "Numero_de_cobro" => count($paso5DetalleDePagos) < 1 ? 1 : count($paso5DetalleDePagos) + 1
                ];
        array_push($data["data"][0]["Paso_5_Detalle_pagos"],$paymentData);
        $responseUdateSaleOrder = $this->zohoService->Update('Sales_Orders', $data, "5344455000004398120");

        // if($paso5DetalleDePagos){
        //     $cobroIDs = array_column($paso5DetalleDePagos, "Cobro_ID");
        //     if (!in_array($responsePaymentById["id"], $cobroIDs)) {//existe en ese array ? no, entonces lo guardo.
                
        //      }
        // }

        return response()->json([
            "responseUdateSaleOrder" => $responseUdateSaleOrder,
            "paso5DetalleDePagos" => $paso5DetalleDePagos,
            $data,
            // "getSalesOrdersById" => $getSalesOrdersById,
            // "responseSuscriptionById" => $responseSuscriptionById,
            "wh" => $responsePaymentById,
            // "contact" => $contact,
            // "sale" => $contract
        ]);
    }
    public function forma1($responseSuscriptionById){
        $invoices = $responseSuscriptionById["invoices"][0];
        $data = [
            "data" => [
                [
                    "Paso_5_Detalle_pagos" => []
                ]
            ]
        ];
        if($invoices){
            
            foreach ($invoices["paidBags"] as $key => $paidBag) {
               $fechaRecortada = date("Y-m-d", strtotime($paidBag["payment"]["createdAt"]));
               $paymentData = [
                   "Fecha_Cobro" => $fechaRecortada,
                   "Cobro_ID" => $paidBag["payment"]["id"],
                   "Numero_de_cobro" => $key + 1
               ];
               array_push($data["data"][0]["Paso_5_Detalle_pagos"],$paymentData);
            }
        }


        $response = $this->zohoService->Update('Sales_Orders', $data, "5344455000004398120");
    }
}
