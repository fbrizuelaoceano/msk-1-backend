<?php

namespace App\Clients;

use App\Dtos\ZohoSettingsDto;
use App\Models\ZohoToken;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;


class ZohoClient implements IClient
{
    private mixed $refreshToken;
    private mixed $clientSecret;
    private mixed $clientId;
    private mixed $accessToken;

    private mixed $settingsDto;
    public function __construct()
    {
        $settings = new ZohoSettingsDto();
        $this->settingsDto = $settings;
        $this->clientId = $settings->getClientId();
        $this->clientSecret = $settings->getClientSecret();
        $this->refreshToken = $settings->getRefreshToken();
    }

    public function getClient(): ?Client
    {
        try {
            $last = ZohoToken::where('client_id', $this->clientId)->orderByDesc('id')->limit(1)->first();

            if ($last) {
                $this->accessToken = $last->access_token;
            }
            $headers = [
                'User-Agent' => Utils::defaultUserAgent(),
                'Authorization' => 'Bearer '.$this->accessToken,
            ];
            $client = new Client([
                'base_uri' => $this->settingsDto->getZohoUrl(),
                'headers' => $headers
            ]);

            $request = new Request('GET', $this->settingsDto->getZohoUrl().'/users');
            /** @var  Response $response */
            $response = $client->sendAsync($request)->wait();
            json_decode($response->getBody()->getContents(), true);

            \Log::info("GetZohoClient:: ", ['SUCCESSFUL']);
            return $client;

        } catch (\Exception | GuzzleException $e) {
            \Log::error("GetZohoClient:: ", [$e->getMessage(), $e->getCode()]);
            if ($e->getCode() == 401) {
                $this->accessTokenFromRefreshToken();
                return $this->getClient();
            }
            dd( $e->getMessage(), $e->getCode());
        }
    }

    public function getUrl()
    {
        return $this->settingsDto->getZohoUrl();
    }
    private function accessTokenFromRefreshToken(): void
    {
        $client = new Client();
        $headers = [
            'Cookie' => 'JSESSIONID=02EF56A6A85DF4CD934696AE42E217BA; _zcsr_tmp=bcb1e04e-87d2-43ff-90e8-1082134655d1; b266a5bf57=a7f15cb1555106de5ac96d088b72e7c8; e188bc05fe=412d04ceb86ecaf57aa7a1d4903c681d; iamcsr=bcb1e04e-87d2-43ff-90e8-1082134655d1'
        ];
        $options = [
            'multipart' => [
                [
                    'name' => 'refresh_token',
                    'contents' => $this->refreshToken
                ],
                [
                    'name' => 'client_id',
                    'contents' => $this->clientId
                ],
                [
                    'name' => 'client_secret',
                    'contents' => $this->clientSecret
                ],
                [
                    'name' => 'grant_type',
                    'contents' => 'refresh_token'
                ]
            ]];
        $request = new Request('POST', 'https://accounts.zoho.com/oauth/v2/token', $headers);
        $response = $client->sendAsync($request, $options)->wait();

        $contents = $response->getBody()->getContents();
        $tokenData = json_decode($contents, true);
        $zohoToken = new ZohoToken();
        $zohoToken->access_token = $tokenData['access_token'];
        $zohoToken->client_id = $this->clientId;
        $zohoToken->expires = date('Y-m-d H:i:s',strtotime('now') + $tokenData['expires_in']);
        $zohoToken->created_at = new \DateTime();
        $zohoToken->updated_at = new \DateTime();
        $zohoToken->save();
    }
}
