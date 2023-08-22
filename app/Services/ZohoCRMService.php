<?php
namespace App\Services;

use App\Models\{Profession,Speciality,TokenPassport};
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZohoCRMService
{
    private $accessToken;
    private $accessTokenReset;
    private $apiBaseUrl;
    private $clientId;
    private $clientSecret;
    private $grantToken;
    private $refreshToken;
    private $urlZoho;

    public function __construct()
    {
        try{
            $this->apiBaseUrl = env('ZOHO_API_BASE_URL');
            $this->clientId = env('ZOHO_CLIENT_ID');
            $this->clientSecret = env('ZOHO_CLIENT_SECRET');
            $this->grantToken = env('ZOHO_GRANT_TOKEN');
            $this->refreshToken = env('ZOHO_REFRESH_TOKEN');
            $this->urlZoho = env('URL_ZOHO');

            $this->getAccessToken();
        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // 'trace' => $e->getTraceAsString(),
            ];

            Log::error("Error en ZohoCRMService__construct: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));

            return response()->json([
                'error' => 'Ocurrió un error en el servidor',
                $err,
            ], 500);
        }
    }

    public function listContactsCrm($page = 1, $limit = 200)
    {

    }
     /* Administracion de tokens */

    private function cleanTokensExpired(){
        $accessTokens = TokenPassport::all();

        foreach($accessTokens as $accessToken){
            $createdAt = Carbon::parse($accessToken->created_at);
            $timeElapsedInSeconds = $createdAt->diffInSeconds(Carbon::now(), false);

            $timeElapsedInHours = $timeElapsedInSeconds / 3600; //3600 segundos == 1 hora
            if($timeElapsedInHours >= 1){
                $accessToken->update(['expired' => 1,'observacion' => 'cleanTokensExpired()']);
            }
        }

        // // Buscar todos los registros con expired en true excepto el primero
        // $tokensToDelete = TokenPassport::where('expired', true)->orderBy('created_at')->skip(1)->get();
        // // Eliminar los registros encontrados
        // foreach ($tokensToDelete as $token) {
        //     $token->delete();
        // }

    }
    private function getAccessToken()
    {
        try {

            $this->cleanTokensExpired();
            $accessToken = TokenPassport::where(['name' => 'Access Token'])->orderBy('created_at', 'desc')->first();

           if (isset($accessToken)) {
               $tokenValidated = $this->isValidToken($accessToken);

                if (empty($this->accessTokenReset) && !$tokenValidated['isExpired']) {
                    $this->accessTokenReset = $tokenValidated['token'];
                    return;
                }

                $this->accessTokenReset = $tokenValidated['token'];
            }

            //Lo cargo por primera vez
            $observacion = 'Se cargo por primera vez';
            $this->generateAccessToken($observacion);
        } catch (Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // 'trace' => $e->getTraceAsString(),
            ];
            Log::error("Error en ZohoCRMService-getAccessToken(): " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));
        }
    }
    private function isValidToken($accessToken)
    {


        $createdAt = Carbon::parse($accessToken->created_at);
        $expiresAt = $createdAt->addHours($accessToken->hours_duration);
        $timeLeft = Carbon::now()->diffInSeconds($expiresAt, false);

         if ($timeLeft <= 300) { /*300seg = 5min | El token expira en menos de 5 minutos*/
            $observacion = 'Se creo porque un token expiraba en 5 minutos. Id del que expiraba: '.$accessToken->id;
            return ['isExpired' => ($timeLeft <= 300), 'token' => $this->generateAccessToken($observacion)];
         }
         //borrar de la db los tokens invalidos o vencidos.
         return ['isExpired' => ($timeLeft <= 300), 'token' => $accessToken->token];
    }
    private function analyzeResponseForErrors($response,$body) {
        if(isset($response["code"]) && $response["code"]==="INVALID_TOKEN"){
            /**
            *   //error de token ?
            *   [] local.ERROR: response:
            *   {
            *       "code": "INVALID_TOKEN",
            *       "details": [],
            *       "message": "invalid oauth token",
            *       "status": "error"
            *   }
            **/
            // Lanza una excepción personalizada con los datos en un campo específico
            Log::error("body: " . "\n" . json_encode($body, JSON_PRETTY_PRINT));
            throw new \Exception("Error de token invalido: ". $body["URL_ZOHO"], 500, new \Exception(json_encode($body)));
        }
        if(isset($response["code"]) && $response["code"]==="INVALID_URL_PATTERN"){
            /**
            *   //enity_id_crm que no existe no es number //asdasdasd
            *   [2023-08-11 11:35:26] local.ERROR: response:
            *   {
            *       "code": "INVALID_URL_PATTERN",
            *       "details": [],
            *       "message": "Please check if the URL trying to access is a correct one",
            *       "status": "error"
            *   }
            **/
            // Lanza una excepción personalizada con los datos en un campo específico
            Log::error("body: " . "\n" . json_encode($body, JSON_PRETTY_PRINT));
            throw new \Exception("Error en la url: ". $body["URL_ZOHO"], 500, new \Exception(json_encode($body)));
        }
        if(isset($response["code"]) && $response["code"]==="MANDATORY_NOT_FOUND"){
            /**
            *   Faltan campos obligatorios
            *   [2023-08-11 14:50:48] local.ERROR: response:
            *   {
            *       "code": "MANDATORY_NOT_FOUND",
            *       "details": {
            *           "api_name": "data"
            *       },
            *       "message": "required field not found",
            *       "status": "error"
            *   }
            **/
            // Lanza una excepción personalizada con los datos en un campo específico
            Log::error("body: " . "\n" . json_encode($body, JSON_PRETTY_PRINT));
            throw new \Exception("Error, campo obligatorio no encontrado en el request: ". json_encode($body["requestArray"]). " Url: ". $body["URL_ZOHO"], 500, new \Exception(json_encode($body)));
        }
        if(isset($response["data"][0]["code"]) && $response["data"][0]["code"]==="INVALID_DATA"){
            /**
            *   // enity_id_crm que no existe en crm /54621354 o error en los campos
            *   [2023-08-11 11:32:27] local.ERROR: response:
            *   {
            *       "data": [
            *           {
            *               "code": "INVALID_DATA",
            *               "details": [],
            *               "message": "the id given seems to be invalid",
            *               "status": "error"
            *           }
            *       ]
            *   }
            **/
            // Lanza una excepción personalizada con los datos en un campo específico
            Log::error("body: " . "\n" . json_encode($body, JSON_PRETTY_PRINT));
            throw new \Exception("Error, data invalida. Error en los campos o el email podria no existir en crm. data: ". json_encode($body["requestArray"]). " Url: ". $body["URL_ZOHO"], 500, new \Exception(json_encode($body)));
        }

        /**
            *   // enity_id_crm existente /5344455000009215960 con actualizacion exitosa
            *   [2023-08-11 11:43:52] local.ERROR: response:
            *   {
            *       "data": [
            *           {
            *               "code": "SUCCESS",
            *               "details": {
            *                   "Modified_Time": "2023-08-11T11:43:51-03:00",
            *                   "Modified_By": {
            *                       "name": "Integraciones Administrador",
            *                       "id": "5344455000001853001"
            *                   },
            *                   "Created_Time": "2023-08-11T10:46:44-03:00",
            *                   "id": "5344455000009215960",
            *                   "Created_By": {
            *                       "name": "Integraciones Administrador",
            *                       "id": "5344455000001853001"
            *                   }
            *               },
            *               "message": "record updated",
            *               "status": "success"
            *           }
            *       ]
            *   }
        */
    }
    /* End Administracion de tokens */


    private function generateAccessToken($observacion = 'Sin observacion.')
    {
        $URL = 'https://' . $this->apiBaseUrl . '/oauth/v2/token?' .
            'refresh_token=' . $this->refreshToken .
            '&client_id=' . $this->clientId .
            '&client_secret=' . $this->clientSecret .
            '&grant_type=' . 'refresh_token';

        $response = Http::post($URL)->json();

        $tokenData = [
            'name' => 'Access Token',
            'token' => $response['access_token'],
            'hours_duration' => floor($response['expires_in'] / 3600), //calcular horas, 3600 = seg
            'observacion' => $observacion
        ];

        TokenPassport::create($tokenData);

        $this->accessToken = $tokenData['token'];

        return $tokenData['token'];
    }
    public function getByEntityId($module, $id)
    {
        $URL_ZOHO = $this->urlZoho . '/' . $module . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->accessTokenReset,
        ])->get($URL_ZOHO)->json();

        return $response;
    }
    public function CreateLeadFunction($leadAttributes)
    {
        $profession = Profession::where('id', $leadAttributes->profession)->first()->name;
        $speciality = Speciality::where('id', $leadAttributes->speciality)->first()->name;

        $leadData = [
            'data' => [
                [
                    'First_Name' => $leadAttributes->name,
                    'Last_Name' => $leadAttributes->last_name,
                    'Phone' => $leadAttributes->phone,
                    'Email' => $leadAttributes->email,
                    'Profesion' => $profession,
                    'Especialidad' => $speciality
                ]
            ]
        ];

        $URL_ZOHO = env('URL_ZOHO') . '/Leads';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->accessToken,
            'Content-Type' => 'application/json'
        ])
            ->post($URL_ZOHO, $leadData)
            ->json();

        return $response;
    }
    public function GetByEmailService($module, $email)
    {
        try {
            $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '/search?email=' . $email;
            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->accessTokenReset,
            ])->get($URL_ZOHO)->json();

            // if(!isset($response["data"])){
            // }

            return $response;
        } catch (Exception $e) {
            Log::error($e);
            return null;
        }
    }

    public function Get($module, $page = 1)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '?page=' . $page;
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->accessTokenReset,
        ])->get($URL_ZOHO)->json();

        return $response;
    }
    public function GetByIdAllDetails($module, $id)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->accessTokenReset,
        ])
            ->get($URL_ZOHO)
            ->json();

        return $response;
    }
    public function GetById($module, $id)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '/search?criteria=(id:equals:' . $id . ')';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->accessTokenReset,
        ])
            ->get($URL_ZOHO)
            ->json();

        return $response;
    }
    public function Create($module, $requestArray)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/' . $module;
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->accessTokenReset,
            'Content-Type' => 'application/json'
        ])
            ->post($URL_ZOHO, $requestArray)
            ->json();


        return $response;
    }
    public function Update($module, $requestArray, $id)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->accessTokenReset,
        ])
            ->put($URL_ZOHO, $requestArray)
            ->json();

        $body = [
            "URL_ZOHO" => $URL_ZOHO,
            "response" => $response,
            "requestArray" => $requestArray
        ];
        // Log::info("ZohoCRMService-Update-body: " . print_r($body, true));
        $this->analyzeResponseForErrors($response,$body);

        return response()->json($response, );
    }
    public function Delete($module, $ids)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/' . $module . '?ids=' . $ids . '&wf_trigger=true';
        if ($ids)
            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->accessTokenReset,
            ])
                ->delete($URL_ZOHO)
                ->json();
        return $response;
    }


}
