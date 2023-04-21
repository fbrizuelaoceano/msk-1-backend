<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use App\Models\Speciality;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// use samples\src\com\zoho\crm\api\initializer;


class ZohoController extends Controller
{
    private $accessToken;

    private $ZOHO_API_BASE_URL = '';
    private $ZOHO_CLIENT_ID = '';
    private $ZOHO_CLIENT_SECRET = '';

    private $ZOHO_GRANT_TOKEN = '';
    private $ZOHO_REFRESH_TOKEN = '';
    private $ZOHO_ACCESS_TOKEN = '';

    private $URL_ZOHO = '';

    public function __construct()
    {
        try {

            $this->ZOHO_API_BASE_URL = env("ZOHO_API_BASE_URL");
            $this->ZOHO_CLIENT_ID = env("ZOHO_CLIENT_ID");
            $this->ZOHO_CLIENT_SECRET = env("ZOHO_CLIENT_SECRET");

            $this->ZOHO_GRANT_TOKEN = env('ZOHO_GRANT_TOKEN');
            $this->ZOHO_REFRESH_TOKEN = env('ZOHO_REFRESH_TOKEN');
            $this->ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');

            $this->URL_ZOHO = env('URL_ZOHO');

        } catch (Exception $e) {
            Log::error($e);
        }
    }

    function CreateRefreshToken()
    {

        $ZOHO_CLIENT_ID = env('ZOHO_CLIENT_ID');
        $ZOHO_CLIENT_SECRET = env('ZOHO_CLIENT_SECRET');
        $ZOHO_GRANT_TOKEN = env('ZOHO_GRANT_TOKEN');
        $ZOHO_API_TOKEN_URL = env('ZOHO_API_TOKEN_URL');
        $ZOHO_REDIRECT_URI = env("ZOHO_REDIRECT_URI");

        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->post($ZOHO_API_TOKEN_URL, [
                'body' =>
                'code=' . $ZOHO_GRANT_TOKEN
                . '&redirect_url=' . $ZOHO_REDIRECT_URI
                . '&client_id=' . $ZOHO_CLIENT_ID
                . '&client_secret=' . $ZOHO_CLIENT_SECRET
                . '&grant_type=authorization_code'
            ])->json();

        return response()->json($response);
    }
    function CreateAccessToken()
    {

        $URL = 'https://' . $this->ZOHO_API_BASE_URL . '/oauth/v2/token?' .
            'refresh_token=' . $this->ZOHO_REFRESH_TOKEN .
            '&client_id=' . $this->ZOHO_CLIENT_ID .
            '&client_secret=' . $this->ZOHO_CLIENT_SECRET .
            '&grant_type=' . 'refresh_token';

        $response = Http::post($URL)->json();

        return response()->json($response);
    }

    function GetLeads()
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Leads';
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
        ])
            ->get($URL_ZOHO)->json();

        return response()->json(
            $response,
        );
    }
    function GetByIdLeads($id)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Leads/search?criteria=(id:equals:' . $id . ')';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
        ])
            ->get($URL_ZOHO)
            ->json();

        return response()->json(
            $response,
        );
    }
    function CreateLeads(Request $request)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Leads';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
            'Content-Type' => 'application/json'
        ])
            ->post($URL_ZOHO, $request->all())
            ->json();

        return response()->json($response, );
    }
    function UpdateLeads(Request $request, $id)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Leads' . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
        ])
            ->put($URL_ZOHO, $request->all())
            ->json();

        return response()->json($response, );
    }
    function DeleteLeads(Request $request, $ids)
    {

        $URL_ZOHO = env('URL_ZOHO') . '/Leads?ids=' . $ids . '&wf_trigger=true';

        if ($ids)

            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
            ])
                ->delete($URL_ZOHO, $request->all())
                ->json();

        return response()->json($response, );
    }

    function GetContacts()
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Contacts';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
        ])
            ->get($URL_ZOHO)->json();

        return response()->json($response, );
    }
    function CreateContacts(Request $request)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Contacts';
        // {
        //     "data": [
        //         {
        //             "code": "SUCCESS",
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
            'Content-Type' => 'application/json'
        ])
            ->post($URL_ZOHO, $request->all())
            ->json();

        return response()->json($response, );
    }
    function GetByIdContacts($id)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Contacts/search?criteria=(id:equals:' . $id . ')';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
        ])
            ->get($URL_ZOHO)
            ->json();

        return response()->json($response, );
    }
    function UpdateContacts(Request $request, $id)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Contacts' . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
        ])
            ->put($URL_ZOHO, $request->all())
            ->json();

        return response()->json($response, );
    }
    function DeleteContacts(Request $request, $ids)
    {

        $URL_ZOHO = env('URL_ZOHO') . '/Contacts?ids=' . $ids . '&wf_trigger=true';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
        ])
            ->delete($URL_ZOHO, $request->all())
            ->json();

        return response()->json($response, );
    }

    function GetContracts()
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Contracts';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
        ])
            ->get($URL_ZOHO)->json();

        return response()->json($response, );
    }
    function GetByIdContracts($id)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Contracts/search?criteria=(id:equals:' . $id . ')';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
        ])
            ->get($URL_ZOHO)
            ->json();

        return response()->json($response, );
    }
    function CreateContracts(Request $request)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Contracts';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
            'Content-Type' => 'application/json'
        ])
            ->post($URL_ZOHO, $request->all())
            ->json();

        return response()->json($response, );
    }
    function UpdateContracts(Request $request, $id)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Contracts' . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
        ])
            ->put($URL_ZOHO, $request->all())
            ->json();

        return response()->json($response, );
    }
    function DeleteContracts(Request $request, $ids)
    {

        $URL_ZOHO = env('URL_ZOHO') . '/Contracts?ids=' . $ids . '&wf_trigger=true';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
        ])
            ->delete($URL_ZOHO, $request->all())
            ->json();

        return response()->json($response, );
    }

    function ConvertLead(Request $request, $id)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/Leads' . '/' . $id . '/actions/convert';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
            'Content-Type' => 'application/json',
        ])
            ->post($URL_ZOHO, $request->all())
            ->json();

        return response()->json($response, );
    }
    function GetByEmail($module, $email)
    {
        try {

            $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '/search?email=' . $email;

            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
            ])
                ->get($URL_ZOHO)->json();

            return response()->json($response, );

        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public function CreateLeadFunction(Request $request)
    {
        $profession = Profession::where('id', $request->profession)->first()->name;
        $speciality = Speciality::where('id', $request->speciality)->first()->name;

        $leadData = [
            'data' => [
                [
                    'First_Name' => $request->name,
                    'Last_Name' => $request->last_name,
                    'Phone' => $request->phone,
                    'Email' => $request->email,
                    'Profesion' => $profession,
                    'Especialidad' => $speciality
                ]
            ]
        ];

        $URL_ZOHO = env('URL_ZOHO') . '/Leads';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN,
            'Content-Type' => 'application/json'
        ])
            ->post($URL_ZOHO, $leadData)
            ->json();

        return $response;
    }

    function prueba()
    {
        $accessToken = Storage::disk('public')->get('/zoho/access_token.txt');

        return response()->json([
            'data' => $accessToken,
        ]);
    }
}