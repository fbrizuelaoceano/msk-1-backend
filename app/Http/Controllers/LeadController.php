<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Exception;

class LeadController extends Controller
{
    private $accessToken;

    private $ZOHO_API_BASE_URL = '';
    private $ZOHO_CLIENT_ID = '';
    private $ZOHO_CLIENT_SECRET = '';

    private $ZOHO_GRANT_TOKEN='';
    private $ZOHO_REFRESH_TOKEN='';
    private $ZOHO_ACCESS_TOKEN='';

    private $URL_ZOHO = '';

    public function __construct()
    {
        try {

            $this->ZOHO_API_BASE_URL = env("ZOHO_API_BASE_URL");
            $this->ZOHO_CLIENT_ID = env("ZOHO_CLIENT_ID");
            $this->ZOHO_CLIENT_SECRET = env("ZOHO_CLIENT_SECRET");
            
            $this->ZOHO_GRANT_TOKEN=env('ZOHO_GRANT_TOKEN');
            $this->ZOHO_REFRESH_TOKEN=env('ZOHO_REFRESH_TOKEN');
            $this->ZOHO_ACCESS_TOKEN=env('ZOHO_ACCESS_TOKEN');

            $this->URL_ZOHO=env('URL_ZOHO');

        }catch(Exception $e){
            Log::error($e);
        }
    }
    
    function CreateLeadMSKCRM(Request $request)
    {
        $request->validate([
            'last_name' => 'required|string',
        ]);
        $leadAttributes = $request->only(Lead::getFormAttributes());

        $newOrUpdatedLead = Lead::updateOrCreate([
            'email' => $leadAttributes['email']
        ], $leadAttributes);

        $zohoService = new ZohoController();

        $response = $zohoService->CreateLeadFunction($leadAttributes);

        // $URL_ZOHO = env('URL_ZOHO').'/Leads';

        // $response = Http::withHeaders([
        //     'Authorization' => 'Zoho-oauthtoken '.env("ZOHO_ACCESS_TOKEN"),
        //     'Content-Type' => 'application/json'
        // ])
        // ->post($URL_ZOHO, $request->all())
        // ->json();

        return response()->json($response,);
    }


    function prueba()
    {
        $accessToken = Storage::disk('public')->get('/zoho/access_token.txt');

        return response()->json([
            'data' => $accessToken,
        ]);
    }
}