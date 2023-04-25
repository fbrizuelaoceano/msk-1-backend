<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use App\Models\Speciality;
use App\Models\TokenPassport;
use Carbon\Carbon;
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
    private $ZOHO_REFRESH_TOKEN_RESET = '';
    private $ZOHO_ACCESS_TOKEN_RESET = '';


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

    /* Administracion de tokens */
    public function CreateRefreshTokenDB()
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

        return $response;
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
    /* End Administracion de tokens */

    function GetLeads()
    {
        $response = $this->Get('Leads');
        return response()->json(
            $response,
        );
    }
    function GetByIdLeads($id)
    {
        $response = $this->GetById('Leads',$id);
        return response()->json(
            $response,
        );
    }
    function CreateLeads(Request $request)
    {
        $response = $this->Create('Leads',$request->all());
        return response()->json(
            $response,
        );
    }
    function UpdateLeads(Request $request, $id)
    {
        $response = $this->Update('Leads',$request->all(),$id);       
        return response()->json(
            $response,
        );
    }
    function DeleteLeads(Request $request, $ids)
    {
        $response = $this->Delete('Leads',$ids);
        return response()->json($response);
    }

    function GetContacts()
    {
        $response = $this->Get('Contacts');
        return response()->json(
            $response,
        );
    }
    function CreateContacts(Request $request)
    {
        $response = $this->Create('Contacts',$request->all());
        return response()->json(
            $response,
        );
    }
    function GetByIdContacts($id)
    {
        $response = $this->GetById('Contacts',$id);
        return response()->json(
            $response,
        );
    }
    function UpdateContacts(Request $request, $id)
    {
        $response = $this->Update('Contacts',$request->all(),$id);       
        return response()->json(
            $response,
        );
    }
    function DeleteContacts(Request $request, $ids)
    {
        $response = $this->Delete('Contacts',$ids);
        return response()->json($response);
    }

    function GetContracts()
    {
        $response = $this->Get('Contracts');
        return response()->json(
            $response,
        );
    }
    function GetByIdContracts($id)
    {
        $response = $this->GetById('Contracts',$id);
        return response()->json(
            $response,
        );
    }
    function CreateContracts(Request $request)
    {
        $response = $this->Create('Contracts',$request->all());
        return response()->json(
            $response,
        );
    }
    function UpdateContracts(Request $request, $id)
    {
        $response = $this->Update('Contracts',$request->all(),$id);       
        return response()->json(
            $response,
        );
    }
    function DeleteContracts(Request $request, $ids)
    {
        $response = $this->Delete('Contacts',$ids);
        return response()->json($response);
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

    /* Desarrollo de Refactorizacion */
        /* Administracion de token */
        function AccessTokenDB(){
            $accessToken = TokenPassport::where(['name' => 'Access Token'])->orderBy('created_at', 'desc')->first();
            if (isset($accessToken)){
                if(empty($this->ZOHO_ACCESS_TOKEN_RESET)){
                    $this->ZOHO_ACCESS_TOKEN_RESET = $accessToken->token;
                }

                $createdAt = Carbon::parse($accessToken->created_at);
                $expiresAt = $createdAt->addHours($accessToken->hours_duration);
                
                $timeLeft = Carbon::now()->diffInSeconds($expiresAt, false);
                if ($timeLeft <= 300) {/*300seg = 5min*/ //Refresh Token Acces
                    // El token expira en menos de 5 minutos
                    $URL = 'https://' . $this->ZOHO_API_BASE_URL . '/oauth/v2/token?' .
                        'refresh_token=' . $this->ZOHO_REFRESH_TOKEN .
                        '&client_id=' . $this->ZOHO_CLIENT_ID .
                        '&client_secret=' . $this->ZOHO_CLIENT_SECRET .
                        '&grant_type=' . 'refresh_token';
                    $response = Http::post($URL)->json();
        
                    $tokenData = [
                        'name' => 'Access Token',
                        'token' => $response['access_token'],
                        'hours_duration' => floor($response['expires_in'] / 3600),//calcular horas, 3600 = seg
                    ];
                    $newAccessToken = TokenPassport::create($tokenData);
                    $this->ZOHO_ACCESS_TOKEN_RESET = $response['access_token'];
                }
            }else{//Lo cargo por primera vez
                $URL = 'https://' . $this->ZOHO_API_BASE_URL . '/oauth/v2/token?' .
                        'refresh_token=' . $this->ZOHO_REFRESH_TOKEN .
                        '&client_id=' . $this->ZOHO_CLIENT_ID .
                        '&client_secret=' . $this->ZOHO_CLIENT_SECRET .
                        '&grant_type=' . 'refresh_token';
                    $response = Http::post($URL)->json();
        
                    $tokenData = [
                        'name' => 'Access Token',
                        'token' => $response['access_token'],
                        'hours_duration' => floor($response['expires_in'] / 3600),//calcular horas, 3600 = seg
                    ];
                    $newAccessToken = TokenPassport::create($tokenData);
                    $this->ZOHO_ACCESS_TOKEN_RESET = $response['access_token'];
            }
        }
        /* End Administracion de token */

    public function Get($module){
        $this->AccessTokenDB();
        $URL_ZOHO = env('URL_ZOHO') . '/'.$module;
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
        ])
            ->get($URL_ZOHO)->json();

        return $response;
    }
    public function GetById($module,$id){
        $this->AccessTokenDB();
        $URL_ZOHO = env('URL_ZOHO') . '/'.$module.'/search?criteria=(id:equals:' . $id . ')';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
        ])
            ->get($URL_ZOHO)
            ->json();

        return $response;
    }
    public function Create($module,$requestArray){
        $this->AccessTokenDB();

        $URL_ZOHO = env('URL_ZOHO') . '/' . $module;
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
            'Content-Type' => 'application/json'
        ])
            ->post($URL_ZOHO, $requestArray)
            ->json();

        return $response;
    }
    public function Update($module,$requestArray,$id){
        $this->AccessTokenDB();

        $URL_ZOHO = env('URL_ZOHO') . '/'. $module . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
        ])
            ->put($URL_ZOHO, $requestArray)
            ->json();

        return response()->json($response, );
    }
    public function Delete($module,$ids){
        $this->AccessTokenDB();

        $URL_ZOHO = env('URL_ZOHO') . '/'.$module.'?ids=' . $ids . '&wf_trigger=true';
        if ($ids)
            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
            ])
                ->delete($URL_ZOHO)
                ->json();
        return $response;
    }

    public function GetByEmailService($module, $email)
    {
        try {
            $this->AccessTokenDB();
            $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '/search?email=' . $email;
            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
            ])
                ->get($URL_ZOHO)->json();

            return $response;

        } catch (Exception $e) {
            Log::error($e);
        }
    }
    /* End Desarrollo de Refactorizacion */

}