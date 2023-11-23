<?php

namespace App\Services;

use App\Clients\IClient;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

abstract class ZohoService
{
    protected string $module;
    protected Client $api;
    protected string $url;

    public function __construct(IClient $client)
    {
        $this->api = $client->getClient();
        $this->url = $client->getUrl();
    }

    public function get()
    {
        $request = new Request('GET', $this->url.'/'.$this->module);
        /** @var  Response $response */
        $response = $this->api->sendAsync($request)->wait();

        return (json_decode($response->getBody()->getContents(), true));
    }

    public function getBy($id)
    {
        $request = new Request('GET', $this->url.'/'.$this->module.'/'.$id);
        /** @var  Response $response */
        $response = $this->api->sendAsync($request)->wait();

        return (json_decode($response->getBody()->getContents(), true));
    }

    public function post($data)
    {
        $request = new Request('POST', $this->url.'/'.$this->module, [], json_encode($data));
        /** @var  Response $response */
        $response = $this->api->sendAsync($request)->wait();

        return (json_decode($response->getBody()->getContents(), true));
    }

    public function put($id, $data)
    {
        $request = new Request('PUT', $this->url.'/'.$this->module.'/'.$id, [], json_encode($data));
        /** @var  Response $response */
        $response = $this->api->sendAsync($request)->wait();

        return (json_decode($response->getBody()->getContents(), true));
    }
}
