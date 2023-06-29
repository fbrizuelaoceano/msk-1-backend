<?php
namespace App\Services;

use App\Models\TokenPassport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ZohoCRMService
{
    private $accessToken;
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

    private function getAccessToken()
    {
        $accessToken = TokenPassport::where(['name' => 'Access Token'])->orderBy('created_at', 'desc')->first();

        if (isset($accessToken)) {

            if (empty($this->accessToken)) {
                $this->accessToken = $accessToken->token;
                return;
            }

            $createdAt = Carbon::parse($accessToken->created_at);
            $expiresAt = $createdAt->addHours($accessToken->hours_duration);
            $timeLeft = Carbon::now()->diffInSeconds($expiresAt, false);

            if ($timeLeft <= 300) { /*300seg = 5min | El token expira en menos de 5 minutos*/
                $this->generateAccessToken();
                return;
            }

        }

        //Lo cargo por primera vez
        $this->generateAccessToken();
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

        $this->accessToken = $response['token'];

        return $response['token'];
    }

    public function get($module)
    {
        $URL_ZOHO = $this->urlZoho . '/' . $module;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->accessToken,
        ])->get($URL_ZOHO)->json();

        return $response;
    }
}