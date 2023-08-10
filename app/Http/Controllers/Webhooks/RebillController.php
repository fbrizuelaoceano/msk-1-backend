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
        try{
            $jsonPayload = file_get_contents('php://input');
            $data = json_decode($jsonPayload, true);

            // Log::info("newPayment: " . print_r($data, true));

            $id = $data['payment']['id'];
            $email = $data['payment']['customer']['email'];
            $status = $data['payment']['status'];

                $token = env('APP_DEBUG') ? env('REBILL_TOKEN_DEV') : env('REBILL_TOKEN_PRD');

                if($status == "SUCCEEDED"){
                    $this->payloadZohoCRMMSK($token,$id);
                }

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token
                ])->get('https://api.rebill.to/v2/payments/' . $id)->json();

                // Log::info("response rebill response: " . print_r($response, true));

        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ];

            Log::error("Error en newPayment: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));

            return response()->json([
                'error' => 'Ocurrió un error en el servidor',
                $err,
            ], 500);
        }
    }
    public function newSubscription(Request $request)
    {
        $jsonPayload = file_get_contents('php://input');
        $data = json_decode($jsonPayload, true);

        // Log::info("newSubscription: " . print_r($data, true));
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
        if($newStatusWebhook == "SUCCEEDED"){
            $this->payloadZohoCRMMSK($token,$idWebhook);
        }

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
        // Log::info("payloadZohoCRMMSK-idPayment: " . print_r($idPayment, true));

        // $idPayment = "5cf7da17-3d17-4912-b45f-fa26da5a7e7b";
        // $apiToken = env('APP_DEBUG') ? env('REBILL_TOKEN_DEV') : env('REBILL_TOKEN_PRD');
        $url = 'https://api.rebill.to/v2/payments/'.$idPayment;
        $responsePaymentById = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $apiToken
        ])->get($url)->json();
        // Log::info("payloadZohoCRMMSK-responsePaymentById: " . print_r($responsePaymentById, true));
        // Log::info("payloadZohoCRMMSK-billingSchedulesId: " . print_r($responsePaymentById["billingSchedulesId"][0], true));

        $responseSuscriptionById = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $apiToken
        ])->get("https://api.rebill.to/v2/subscriptions/".$responsePaymentById["billingSchedulesId"][0])->json();
        // Log::info("payloadZohoCRMMSK-responseSuscriptionById: " . print_r($responseSuscriptionById, true));

        $soNumber = isset($responseSuscriptionById["metadataObject"]["so_number"]) ? str_replace("x", "", $responseSuscriptionById["metadataObject"]["so_number"]) : "";
        $query = "Sales_Orders/search?criteria=(otro_so:equals:".$soNumber.")";
        $getSalesOrdersBySO_OM = $this->zohoService->get($query);
        $getSalesOrdersById = $this->zohoService->GetByIdAllDetails('Sales_Orders',$getSalesOrdersBySO_OM['data'][0]["id"]);
        // Log::info("payloadZohoCRMMSK-getSalesOrdersById: " . print_r($getSalesOrdersById, true));
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
        // $responseUdateSaleOrder = $this->zohoService->Update('Sales_Orders', $data, $getSalesOrdersBySO_OM['data'][0]["id"]);
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
                    "Numero_de_cobro" => count($paso5DetalleDePagos) < 1 ? 1 : count($paso5DetalleDePagos) + 1,
                    "Origen_Pago" => "Rebill",
                ];
        array_push($data["data"][0]["Paso_5_Detalle_pagos"],$paymentData);
        // *#endregion*
        // Log::info("payloadZohoCRMMSK-data: " . print_r($data, true));

        //Actualizacion de sale order en zrmzohomsk
        $responseUdateSaleOrder = $this->zohoService->Update('Sales_Orders', $data, $getSalesOrdersById['data'][0]["id"]);
        // Log::info("payloadZohoCRMMSK-responseUdateSaleOrder: " . print_r($responseUdateSaleOrder, true));

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


// [2023-07-05 20:56:23] local.INFO: newPayment: Array
// (
//     [payment] => Array
//         (
//             [id] => 1c086e99-8aac-4b6c-8504-5d484aabf23d
//             [status] => SUCCEEDED
//             [amount] => 1000
//             [currency] => CLP
//             [createdAt] => 2023-07-05T20:56:21.174Z
//             [paymentMethod] => CARD
//             [customer] => Array
//                 (
//                     [email] => talk.gtg@gmail.com
//                 )

//             [card] => Array
//                 (
//                     [id] => f8269634-4d53-4819-8790-2e7834b9269b
//                 )

//             [organization] => Array
//                 (
//                     [id] => 679d8e12-e0ad-4052-bc9e-eb78f956ce7e
//                 )

//             [gateway] => Array
//                 (
//                     [type] => stripe
//                     [country] => UK
//                 )

//         )

//     [webhook] => Array
//         (
//             [id] => 4975af23-c5f7-4641-a082-a4853e9029d7
//             [event] => new-payment
//             [url] => https://dev.msklatam.com/msk-laravel/public/api/webhook/rebill/newPayment
//             [logId] => 6fe6548d-a280-40a1-b3eb-8229daef8684
//         )

// )

// [2023-07-05 20:56:23] local.ERROR: Error en GetProfile: Undefined array key "SUCCEEDED"
// {
//     "message": "Undefined array key \"SUCCEEDED\"",
//     "exception": "ErrorException",
//     "line": 44,
//     "file": "\/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/app\/Http\/Controllers\/Webhooks\/RebillController.php",
//     "trace": "#0 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Bootstrap\/HandleExceptions.php(254): Illuminate\\Foundation\\Bootstrap\\HandleExceptions->handleError(2, 'Undefined array...', '\/home\/customer\/...', 44)\n#1 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/app\/Http\/Controllers\/Webhooks\/RebillController.php(44): Illuminate\\Foundation\\Bootstrap\\HandleExceptions->Illuminate\\Foundation\\Bootstrap\\{closure}(2, 'Undefined array...', '\/home\/customer\/...', 44)\n#2 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Controller.php(54): App\\Http\\Controllers\\Webhooks\\RebillController->newPayment(Object(Illuminate\\Http\\Request))\n#3 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/ControllerDispatcher.php(43): Illuminate\\Routing\\Controller->callAction('newPayment', Array)\n#4 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Route.php(259): Illuminate\\Routing\\ControllerDispatcher->dispatch(Object(Illuminate\\Routing\\Route), Object(App\\Http\\Controllers\\Webhooks\\RebillController), 'newPayment')\n#5 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Route.php(205): Illuminate\\Routing\\Route->runController()\n#6 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Router.php(798): Illuminate\\Routing\\Route->run()\n#7 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(141): Illuminate\\Routing\\Router->Illuminate\\Routing\\{closure}(Object(Illuminate\\Http\\Request))\n#8 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Middleware\/SubstituteBindings.php(50): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#9 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(180): Illuminate\\Routing\\Middleware\\SubstituteBindings->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#10 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Middleware\/ThrottleRequests.php(152): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#11 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Middleware\/ThrottleRequests.php(118): Illuminate\\Routing\\Middleware\\ThrottleRequests->handleRequest(Object(Illuminate\\Http\\Request), Object(Closure), Array)\n#12 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Middleware\/ThrottleRequests.php(80): Illuminate\\Routing\\Middleware\\ThrottleRequests->handleRequestUsingNamedLimiter(Object(Illuminate\\Http\\Request), Object(Closure), 'api', Object(Closure))\n#13 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(180): Illuminate\\Routing\\Middleware\\ThrottleRequests->handle(Object(Illuminate\\Http\\Request), Object(Closure), 'api')\n#14 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(116): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#15 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Router.php(797): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#16 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Router.php(776): Illuminate\\Routing\\Router->runRouteWithinStack(Object(Illuminate\\Routing\\Route), Object(Illuminate\\Http\\Request))\n#17 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Router.php(740): Illuminate\\Routing\\Router->runRoute(Object(Illuminate\\Http\\Request), Object(Illuminate\\Routing\\Route))\n#18 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Router.php(729): Illuminate\\Routing\\Router->dispatchToRoute(Object(Illuminate\\Http\\Request))\n#19 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Kernel.php(200): Illuminate\\Routing\\Router->dispatch(Object(Illuminate\\Http\\Request))\n#20 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(141): Illuminate\\Foundation\\Http\\Kernel->Illuminate\\Foundation\\Http\\{closure}(Object(Illuminate\\Http\\Request))\n#21 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/app\/Http\/Middleware\/CorsMiddleware.php(18): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#22 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(180): App\\Http\\Middleware\\CorsMiddleware->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#23 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#24 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/ConvertEmptyStringsToNull.php(31): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#25 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(180): Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#26 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#27 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/TrimStrings.php(40): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#28 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(180): Illuminate\\Foundation\\Http\\Middleware\\TrimStrings->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#29 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/ValidatePostSize.php(27): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#30 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(180): Illuminate\\Foundation\\Http\\Middleware\\ValidatePostSize->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#31 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/PreventRequestsDuringMaintenance.php(86): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#32 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(180): Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#33 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Http\/Middleware\/HandleCors.php(62): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#34 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(180): Illuminate\\Http\\Middleware\\HandleCors->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#35 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Http\/Middleware\/TrustProxies.php(39): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#36 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(180): Illuminate\\Http\\Middleware\\TrustProxies->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#37 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php(116): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#38 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Kernel.php(175): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#39 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Kernel.php(144): Illuminate\\Foundation\\Http\\Kernel->sendRequestThroughRouter(Object(Illuminate\\Http\\Request))\n#40 \/home\/customer\/www\/dev.msklatam.com\/public_html\/msk-laravel\/public\/index.php(51): Illuminate\\Foundation\\Http\\Kernel->handle(Object(Illuminate\\Http\\Request))\n#41 {main}"
// }
