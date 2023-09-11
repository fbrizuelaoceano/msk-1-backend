<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactUsRequest;
use App\Http\Requests\LeadHomeNewsletterRequest;
use App\Models\{Career, Lead, MethodContact, ProductCRM, Profession, Speciality, TokenPassport};
use App\Services\ZohoCRMService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ReCaptcha\ReCaptcha;

// use samples\src\com\zoho\crm\api\initializer;


class ZohoController extends Controller
{
    private $accessToken;
    private $zohoService;

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

            $this->zohoService = $service;
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
    public function CreateLeadHomeContactUs(ContactUsRequest $request)
    {

        $token = $request->input('recaptcha_token');
        $recaptcha = new ReCaptcha(env('RECAPTCHA_SECRET_KEY'));
        $response = $recaptcha->verify($token);

        try{
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
                        "Pais" => $request->Pais,
                        "Cursos_consultados" => isset($request->Cursos_consultados) ? $request->Cursos_consultados : null,
                        "Carrera_de_estudio" => isset($request->career) ? $request->career : null,
                        "A_o_de_estudio" => isset($request->year) ? $request->year : null,

                ]
            ]
        ];

            //Log::channel('zoho-leads')->info("data: " . print_r($data, true));

        $response = $this->Create('Leads', $data);

            //Log::channel('zoho-leads')->info("data: " . print_r($response, true));

            if (!empty($request->Profesion))
                $profession = Profession::where(['name' => $request->Profesion])->first();
            if (isset($profession->name) && $profession->name !== "Estudiante") {
                if (!empty($request->Especialidad))
                    $specialty = Speciality::where(['name' => $request->Especialidad])->first();
            }
            if (isset($profession->name) && $profession->name === "Estudiante") {
                if (!empty($request->career))
                    $career = Career::where(['name' => $request->career])->first();
            }

            if (!empty($request->Preferencia_de_contactaci_n))
                $contactMethod = MethodContact::where(['name' => $request->Preferencia_de_contactaci_n])->first();

            $newLead = Lead::create([
                "email" => $request->Email,
                "last_name" => $request->Last_Name,
                "name" => $request->First_Name,
                "profession" => isset($profession->id) ? $profession->id : null,
                "speciality" => isset($specialty->id) ? $specialty->id : null,
                "phone" => $request->Phone,
                "method_contact" => isset($contactMethod->id) ? $contactMethod->id : null,
                "entity_id_crm" => $response['data'][0]['details']['id'],
                'country' => $request->Pais,
                "career" => isset($career->id) ? $career->id : '',
                'year' => isset($request->year) ? $request->year : ''
            ]);

            return response()->json([
                "crm" => $response,
                "msk" => $newLead
            ]);
        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // 'trace' => $e->getTraceAsString(),
            ];

            Log::error("Error en CreateLeadHomeContactUs: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));
            $status = 500;
            return response()->json([
                'error' => $err,
                "status" => $status
            ], $status);
        }
    }
    public function CreateLeadHomeNewsletter(LeadHomeNewsletterRequest $request)
    {

        $request->validate([
            'Email' => 'required|string|email',
        ]);

        try{

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
        $profession = Profession::where('name', $request->Profesion)->first();
        $specialty = Speciality::where('name', $request->Especialidad)->first();
        $career = Career::where('name', $request->Career)->first();

        if (!$leadExists) { // no se encontró ningún registro con ese email
                $response = $this->Create('Leads', $data);
                $leadMSK = new Lead();
                $leadMSK->email = $request->Email;

                if (isset($response['data'][0]['code']) && $response['data'][0]['code'] == "SUCCESS") {
                    $leadMSK->entity_id_crm = $response['data'][0]['details']['id'];
                }

                // $newLead = Lead::Create($leadMSK->toArray());
                $newLead = Lead::create([
                    "email" => $request->Email,
                    "last_name" => $request->Last_Name,
                    "name" => $request->First_Name,
                    "profession" => isset($profession->id) ? $profession->id : null,
                    "speciality" => isset($specialty->id) ? $specialty->id : null,
                    "phone" => $request->Phone,
                    "method_contact" => 2,
                    "entity_id_crm" => $response['data'][0]['details']['id'],
                    'country' => $request->Pais,
                    "career" => isset($career->id) ? $career->id : '',
                    'year' => isset($request->Year) ? $request->Year : '',
                    'other_profession' => isset($request->Otra_profesion) ? $request->Otra_profesion : null,
                    'other_speciality' => isset($request->Otra_especialidad) ? $request->Otra_especialidad : null

                ]);

                return response()->json([
                    "crm" => $response,
                    "msk" => $newLead
                ]);
            } else { // se encontró un registro con ese email
                $leadExists->update([
                    "email" => $request->Email,
                    "last_name" => $request->Last_Name,
                    "name" => $request->First_Name,
                    "profession" => isset($profession->id) ? $profession->id : null,
                    "speciality" => isset($specialty->id) ? $specialty->id : null,
                    "phone" => $request->Phone,
                    "method_contact" => 2,
                    'country' => $request->Pais,
                    "career" => isset($career->id) ? $career->id : '',
                    'year' => isset($request->Year) ? $request->Year : '',
                    'other_profession' => isset($request->Otra_profesion) ? $request->Otra_profesion : null,
                    'other_speciality' => isset($request->Otra_especialidad) ? $request->Otra_especialidad : null

                ]);
                // Cargar el modelo nuevamente para obtener los datos actualizados
                $leadExists->refresh();

                if (isset($leadExists->entity_id_crm) && $leadExists->entity_id_crm !== '') { //Tiene id en crm, existe en crm, entonces actualizo
                    $response = $this->zohoService->Update('Leads', $data, $leadExists->entity_id_crm);
                } else { // No tiene id de crm, no existe en crm, entonces lo creo
                    $response = $this->Create('Leads', $data);
                    if (isset($response['data'][0]['code']) && $response['data'][0]['code'] == "SUCCESS") {
                        // $leadExists->entity_id_crm = $response['data'][0]['details']['id'];
                        // $leadExists->save();
                        $leadExists->update([
                            "entity_id_crm" => $response['data'][0]['details']['id'],
                        ]);
                    }
                }
                return response()->json([
                    "crm" => $response,
                    "msk" => $leadExists
                ]);
            }
        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // 'trace' => $e->getTraceAsString(),
            ];
            Log::error("Error en CreateLeadHomeNewsletter: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));

            $status = 500;
            return response()->json([
                'error' => 'Ocurrió un error en el servidor',
                'details' => $err,
                $status
            ], $status);
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

                $observacion = 'ZohoController: Se creo porque un token expiraba en 5 minutos. Id del que expiraba: ' . $accessToken->id;
                $tokenData = [
                    'name' => 'Access Token',
                    'token' => $response['access_token'],
                    'hours_duration' => floor($response['expires_in'] / 3600),
                    //calcular horas, 3600 = seg
                    'observacion' => $observacion
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

            $observacion = 'ZohoController: Se lo cargo por primera vez';
            $tokenData = [
                'name' => 'Access Token',
                'token' => $response['access_token'],
                'hours_duration' => floor($response['expires_in'] / 3600),
                //calcular horas, 3600 = seg
                'observacion' => $observacion
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
        // Log::info("ZohoController-Update-body: " . print_r($body, true));

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

            // $products = $this->zohoService->Get('Products', 2);

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


    public function getProductsCRM($page)
    {
        $products = $this->zohoService->Get('Products', $page);

        foreach ($products['data'] as $p) {
            ProductCRM::updateOrCreate(['product_code' => $p['Product_Code']], [
                'product_code' => $p['Product_Code'],
                'cedente_code' => $p['C_digo_de_Curso_Cedente'],
                'platform' => $p['Plataforma_enrolamiento'],
                'platform_url' => isset($p['URL_plataforma']) ? $p['URL_plataforma'] : null,
                'entity_id' => $p['id'],
            ]);
        }

        dump($products);

    }
}
