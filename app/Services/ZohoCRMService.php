<?php
namespace App\Services;

use App\Models\Profession;
use App\Models\Speciality;
use App\Models\TokenPassport;
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
        $this->apiBaseUrl = env('ZOHO_API_BASE_URL');
        $this->clientId = env('ZOHO_CLIENT_ID');
        $this->clientSecret = env('ZOHO_CLIENT_SECRET');
        $this->grantToken = env('ZOHO_GRANT_TOKEN');
        $this->refreshToken = env('ZOHO_REFRESH_TOKEN');
        $this->urlZoho = env('URL_ZOHO');

        $this->getAccessToken();
    }

    public function listContactsCrm($page = 1, $limit = 200)
    {

    }

    private function getAccessToken()
    {
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
        $this->generateAccessToken();
    }

    private function isValidToken($accessToken)
    {
        $createdAt = Carbon::parse($accessToken->created_at);
        $expiresAt = $createdAt->addHours($accessToken->hours_duration);
        $timeLeft = Carbon::now()->diffInSeconds($expiresAt, false);

        if ($timeLeft <= 300) { /*300seg = 5min | El token expira en menos de 5 minutos*/
            return ['isExpired' => ($timeLeft <= 300), 'token' => $this->generateAccessToken()];
        }

        return ['isExpired' => ($timeLeft <= 300), 'token' => $accessToken->token];
    }

    private function generateAccessToken()
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
            ])
                ->get($URL_ZOHO)->json();

            return $response;

        } catch (Exception $e) {
            Log::error($e);
        }
    }

    /* Administracion de tokens */
    public function Get($module)
    {
        $URL_ZOHO = env('URL_ZOHO') . '/' . $module;
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
            $URL_ZOHO,
            $response,
            $requestArray
        ];
        Log::info("ZohoCRMService-Update-body: " . print_r($body, true));

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
    /* End Administracion de tokens */

}
