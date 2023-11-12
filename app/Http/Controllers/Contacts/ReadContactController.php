<?php

namespace App\Http\Controllers\Contacts;

use App\Clients\ZohoClient;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use App\Services\ZohoContactsService;

class ReadContactController extends Controller
{
    private ZohoContactsService $service;

    public function __construct(ZohoClient $client)
    {
        $this->service = new ZohoContactsService($client);
    }
    
    public function index(): \Illuminate\Http\JsonResponse
    {
        try{
            $data = $this->service->get();

            return Responser::success($data);
        } catch (\Exception $e) {
            return Responser::error($e);
        }
    }
}
