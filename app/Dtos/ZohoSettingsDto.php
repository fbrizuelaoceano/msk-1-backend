<?php

namespace App\Dtos;

class ZohoSettingsDto
{
    private mixed $accessToken;
    private mixed $refreshToken;
    private mixed $clientId;
    private mixed $clientSecret;

    private mixed $zohoUrl;

    public function __construct()
    {
        $this->clientSecret = config('zoho.ZOHO_CLIENT_SECRET');
        $this->clientId = config('zoho.ZOHO_CLIENT_ID');
        $this->refreshToken = config('zoho.ZOHO_REFRESH_TOKEN');
        $this->zohoUrl = config('zoho.URL_ZOHO');
    }

    public function getAccessToken(): mixed
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): mixed
    {
        return $this->refreshToken;
    }

    public function getClientId(): mixed
    {
        return $this->clientId;
    }

    public function getClientSecret(): mixed
    {
        return $this->clientSecret;
    }

    public function getZohoUrl(): mixed
    {
        return $this->zohoUrl;
    }
    public function setAccessToken(mixed $accessToken): void
    {
        $this->accessToken = $accessToken;
    }
}