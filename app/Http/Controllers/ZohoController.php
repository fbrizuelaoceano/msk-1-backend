<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\MethodContact;
use App\Models\ProductCRM;
use App\Models\Profession;
use App\Models\Speciality;
use App\Models\TokenPassport;
use App\Services\ZohoCRMService;
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
    private $service;

    private $ZOHO_API_BASE_URL = '';
    private $ZOHO_CLIENT_ID = '';
    private $ZOHO_CLIENT_SECRET = '';

    private $ZOHO_GRANT_TOKEN = '';
    private $ZOHO_REFRESH_TOKEN = '';
    private $ZOHO_ACCESS_TOKEN = '';
    private $ZOHO_REFRESH_TOKEN_RESET = '';
    private $ZOHO_ACCESS_TOKEN_RESET = '';

    private $URL_ZOHO = '';

    public function __construct(ZohoCRMService $service)
    {
        try {
            $this->service = $service;
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
        $response = $this->GetById('Leads', $id);
        return response()->json(
            $response,
        );
    }
    function CreateLeads(Request $request)
    {
        $response = $this->Create('Leads', $request->all());
        return response()->json(
            $response,
        );
    }
    function UpdateLeads(Request $request, $id)
    {
        $response = $this->Update('Leads', $request->all(), $id);
        return response()->json(
            $response,
        );
    }
    function DeleteLeads(Request $request, $ids)
    {
        $response = $this->Delete('Leads', $ids);
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
        $response = $this->Create('Contacts', $request->all());
        return response()->json(
            $response,
        );
    }
    function GetByIdContacts($id)
    {
        $response = $this->GetById('Contacts', $id);
        return response()->json(
            $response,
        );
    }
    function UpdateContacts(Request $request, $id)
    {
        $response = $this->Update('Contacts', $request->all(), $id);
        return response()->json(
            $response
        );
    }
    function DeleteContacts(Request $request, $ids)
    {
        $response = $this->Delete('Contacts', $ids);
        return response()->json($response);
    }

    function GetProducts()
    {
        $response = $this->Get('Products');
        return response()->json(
            $response,
        );
    }
    function GetByIdProducts($id)
    {
        // byid
        // {
        //     "Avales": null,
        // }
        $response = $this->GetById('Products', $id);
        return response()->json(
            $response,
        );
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
        $response = $this->GetById('Contracts', $id);
        return response()->json(
            $response,
        );
    }
    function CreateContracts(Request $request)
    {
        $response = $this->Create('Contracts', $request->all());
        return response()->json(
            $response,
        );
    }
    function UpdateContracts(Request $request, $id)
    {
        $response = $this->Update('Contracts', $request->all(), $id);
        return response()->json(
            $response,
        );
    }
    function DeleteContracts(Request $request, $ids)
    {
        $response = $this->Delete('Contacts', $ids);
        return response()->json($response);
    }

    function GetQuotes()
    {
        $response = $this->Get('Quotes');
        return response()->json(
            $response,
        );
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
    public function CreateLeadHomeContactUs(Request $request)
    {

        // $request->validate([
        //     'Email' => 'required|string|email',
        //     'Last_Name' => 'required|string',
        // ]);

        // // $lead = Lead::where(['Email'=> $request->Email ])->first();
        // // $response = $this->GetByEmailService('Leads',$request->Email);
        // // if ($response == null ) {//No esta en CRM
        // $data = [
        //     "data" => [
        //         [
        //             "Email" => $request->Email,
        //             "Last_Name" => $request->Last_Name,
        //             "Name" => $request->Name,
        //             "Profesion" => $request->Profesion,
        //             "Especialidad" => $request->Especialidad,
        //             "Phone" => $request->Phone,
        //             "Description" => $request->Description,
        //             "Preferencia_de_contactaci_n" => [$request->Preferencia_de_contactaci_n],
        //         ]
        //     ]
        // ];

        // if (!empty($request->Otra_profesion)) {
        //     $data['data'][0]['Otra_profesion'] = $request->Otra_profesion;
        // }

        // if (!empty($request->Otra_especialidad)) {
        //     $data['data'][0]['Otra_especialidad'] = $request->Otra_especialidad;
        // }

        // $response = $this->Create('Leads', $data);
        // // }

        // if (!empty($request->Profesion))
        //     $profession = Profession::where(['name' => $request->Profesion])->first();
        // if (!empty($request->Especialidad))
        //     $specialty = Speciality::where(['name' => $request->Especialidad])->first();
        // if (!empty($request->Preferencia_de_contactaci_n))
        //     $contactMethod = MethodContact::where(['name' => $request->Preferencia_de_contactaci_n])->first();

        // $newLead = Lead::Create([
        //     "email" => $request->Email,
        //     "last_name" => $request->Last_Name,
        //     "name" => $request->Name,
        //     "profession" => isset($profession->id) ? $profession->id : '',
        //     "speciality" => isset($specialty->id) ? $specialty->id : '',
        //     "phone" => $request->Phone,
        //     "method_contact" => isset($contactMethod->id) ? $contactMethod->id : '',

        //     // "entity_id_crm" => $response->id,//Hay que asociar el id del crm
        //     // "Message" => $request->Message,//Crear un campo para esto
        // ]);
        $request->validate([
            'Email' => 'required|string|email',
            'Last_Name' => 'required|string',
        ]);

        // $lead = Lead::where(['Email'=> $request->Email ])->first();
        // $response = $this->GetByEmailService('Leads',$request->Email);
        // if ($response == null ) {//No esta en CRM


        $data = [
            "data" => [
                [
                    "Phone" => $request->Phone,
                    "Description" => $request->Description,
                    "Preferencia_de_contactaci_n" => [$request->Preferencia_de_contactaci_n],
                    "First_Name" => $request->First_Name,
                    "Last_Name" => $request->Last_Name,
                    "Email" => $request->Email,
                    "Profesion" => $request->Profesion,
                    "Especialidad" => $request->Especialidad,
                    "Otra_profesion" => $request->Otra_profesion,
                    "Otra_especialidad" => $request->Otra_especialidad,
                    "Ad_Account" => isset($request->utm_source) ? $request->utm_source : null,
                    "Ad_Set" => isset($request->utm_medium) ? $request->utm_medium : null,
                    "Ad_Campaign" => isset($request->utm_campaign) ? $request->utm_campaign : null,
                    "Ad_Name" => isset($request->utm_content) ? $request->utm_content : null,
                    "Pais" => $request->Pais

                ]
            ]
        ];

        //Log::channel('zoho-leads')->info("data: " . print_r($data, true));

        $response = $this->Create('Leads', $data);

        //Log::channel('zoho-leads')->info("data: " . print_r($response, true));

        if (!empty($request->Profesion))
            $profession = Profession::where(['name' => $request->Profesion])->first();
        if (!empty($request->Especialidad))
            $specialty = Speciality::where(['name' => $request->Especialidad])->first();
        if (!empty($request->Preferencia_de_contactaci_n))
            $contactMethod = MethodContact::where(['name' => $request->Preferencia_de_contactaci_n])->first();

        $newLead = Lead::create([
            "email" => $request->Email,
            "last_name" => $request->Last_Name,
            "name" => $request->First_Name,
            "profession" => isset($profession->id) ? $profession->id : '',
            "speciality" => isset($specialty->id) ? $specialty->id : '',
            "phone" => $request->Phone,
            "method_contact" => isset($contactMethod->id) ? $contactMethod->id : '',
            "entity_id_crm" => $response['data'][0]['details']['id'],
            'country' => $request->Pais
        ]);

        return response()->json([
            "crm" => $response,
            "msk" => $newLead
        ]);
    }
    public function CreateLeadHomeNewsletter(Request $request)
    {

        $request->validate([
            'Email' => 'required|string|email',
        ]);

        $data = [
            "data" => [
                [
                    "First_Name" => $request->First_Name,
                    "Last_Name" => $request->Last_Name,
                    "Email" => $request->Email,
                    "Profesion" => $request->Profesion,
                    "Especialidad" => $request->Especialidad,
                    "Otra_profesion" => $request->Otra_profesion,
                    "Otra_especialidad" => $request->Otra_especialidad,
                    "Temas_de_interes" => $request->Temas_de_interes,
                    "Lead_Source" => "Suscriptor newsletter",
                    "Ad_Account" => isset($request->utm_source) ? $request->utm_source : null,
                    "Ad_Set" => isset($request->utm_medium) ? $request->utm_medium : null,
                    "Ad_Campaign" => isset($request->utm_campaign) ? $request->utm_campaign : null,
                    "Ad_Name" => isset($request->utm_content) ? $request->utm_content : null,

                ]
            ]
        ];
        $leadExists = Lead::where(['email' => $request->Email])->first();
        if (!$leadExists) { // no se encontró ningún registro con ese email
            $response = $this->Create('Leads', $data);

            $leadMSK = new Lead();
            $leadMSK->email = $request->Email;

            if (isset($response['data'][0]['code']) && $response['data'][0]['code'] == "SUCCESS") {
                $leadMSK->entity_id_crm = $response['data'][0]['details']['id'];
            }

            $newLead = Lead::Create($leadMSK->toArray());

            return response()->json([
                "crm" => $response,
                "msk" => $newLead
            ]);
        } else { // se encontró un registro con ese email
            if (isset($leadExists->entity_id_crm)) { //Tiene id en crm, existe en crm, entonces actualizo
                $response = $this->Update('Leads', $data, $leadExists->entity_id_crm);
            } else { // No tiene id de crm, no existe en crm, entonces lo creo
                $response = $this->Create('Leads', $data);
                if (isset($response['data'][0]['code']) && $response['data'][0]['code'] == "SUCCESS") {
                    $leadExists->entity_id_crm = $response['data'][0]['details']['id'];
                    $leadExists->save();
                }
            }
            return response()->json([
                "crm" => $response,
                "msk" => $leadExists
            ]);
        }
    }
    /* Desarrollo de Refactorizacion */
    /* Administracion de token */
    function AccessTokenDB()
    {
        // $accessToken = TokenPassport::where(['name' => 'Access Token'])->orderBy('created_at', 'desc')->first();
        if (isset($accessToken)) {
            if (empty($this->ZOHO_ACCESS_TOKEN_RESET)) {
                $this->ZOHO_ACCESS_TOKEN_RESET = $accessToken->token;
            }

            $createdAt = Carbon::parse($accessToken->created_at);
            $expiresAt = $createdAt->addHours($accessToken->hours_duration);

            $timeLeft = Carbon::now()->diffInSeconds($expiresAt, false);
            if ($timeLeft <= 300) { /*300seg = 5min*///Refresh Token Acces
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
                    'hours_duration' => floor($response['expires_in'] / 3600), //calcular horas, 3600 = seg
                ];
                $newAccessToken = TokenPassport::create($tokenData);
                $this->ZOHO_ACCESS_TOKEN_RESET = $response['access_token'];
            }
        } else { //Lo cargo por primera vez
            $URL = 'https://' . $this->ZOHO_API_BASE_URL . '/oauth/v2/token?' .
                'refresh_token=' . $this->ZOHO_REFRESH_TOKEN .
                '&client_id=' . $this->ZOHO_CLIENT_ID .
                '&client_secret=' . $this->ZOHO_CLIENT_SECRET .
                '&grant_type=' . 'refresh_token';
            $response = Http::post($URL)->json();

            $tokenData = [
                'name' => 'Access Token',
                'token' => $response['access_token'],
                'hours_duration' => floor($response['expires_in'] / 3600), //calcular horas, 3600 = seg
            ];
            $newAccessToken = TokenPassport::create($tokenData);
            $this->ZOHO_ACCESS_TOKEN_RESET = $response['access_token'];
        }
    }
    /* End Administracion de token */

    public function Get($module)
    {
        $this->AccessTokenDB();
        $URL_ZOHO = env('URL_ZOHO') . '/' . $module;
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
        ])
            ->get($URL_ZOHO)->json();

        return $response;
    }

    public function GetByIdAllDetails($module, $id)
    {
        $this->AccessTokenDB();
        $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
        ])
        ->get($URL_ZOHO)
        ->json();

        return $response;
    }
    public function GetById($module, $id)
    {
        $this->AccessTokenDB();
        $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '/search?criteria=(id:equals:' . $id . ')';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
        ])
            ->get($URL_ZOHO)
            ->json();

        return $response;
    }
    public function Create($module, $requestArray)
    {
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
    public function Update($module, $requestArray, $id)
    {
        $this->AccessTokenDB();

        $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
        ])
            ->put($URL_ZOHO, $requestArray)
            ->json();

        $body = [
            $URL_ZOHO,
            $response,
            $requestArray
        ];
        Log::info("ZohoController-Update-body: " . print_r($body, true));

        return response()->json($response, );
    }
    public function Delete($module, $ids)
    {
        $this->AccessTokenDB();

        $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '?ids=' . $ids . '&wf_trigger=true';
        if ($ids)
            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
            ])
                ->delete($URL_ZOHO)
                ->json();
        return $response;
    }

    public function GetCursadaService($id)
    {
        try {
            $this->AccessTokenDB();
            $URL_ZOHO = env('URL_ZOHO') . '/Contacts/' . $id;
            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->ZOHO_ACCESS_TOKEN_RESET,
            ])
                ->get($URL_ZOHO)->json();

            return $response;

        } catch (Exception $e) {
            Log::error($e);
        }
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


    public function getProductsCRM()
    {
        $products = $this->service->Get('Products', 2);

        foreach ($products['data'] as $p) {
            ProductCRM::updateOrCreate(['product_code' => $p['Product_Code']], [
                'product_code' => $p['Product_Code'],
                'cedente_code' => $p['C_digo_de_Curso_Cedente'],
                'platform' => $p['Plataforma_enrolamiento'],
                'platform_url' => $p['URL_plataforma'],
                'entity_id' => $p['id'],
            ]);
        }

    }
}