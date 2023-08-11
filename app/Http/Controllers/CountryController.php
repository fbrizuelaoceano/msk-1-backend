<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    //
    public function getCountryByIP(Request $request){
        try{
            $VITE_IP_API_KEY = "OE5hxPrfwddjYYP";
            // $ip = $_POST['ip'];
            $ip = $request->ip;
            $IP_API = "https://pro.ip-api.com/json/".$ip."?fields=61439&key=" . $VITE_IP_API_KEY;
            // Log::info("CountryController-getCountryByIP-IP_API: " . print_r($IP_API, true));

            $ch = curl_init($IP_API);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);
            return response()->json([
                "data" => strtolower($data['countryCode'])
            ]);
        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // 'trace' => $e->getTraceAsString(),
            ];

            Log::error("Error en CountryController-getCountryByIP: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));
            return response()->json([
                "error" => "Hubo un error en el servidor, revise los logs."
            ]);
        }
    }
}
