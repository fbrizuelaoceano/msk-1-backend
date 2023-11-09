<?php

namespace App\Http\Controllers\Leads;

use App\Clients\ZohoClient;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use App\Services\ZohoLeadsService;

class ReadLeadController extends Controller
{
    private ZohoLeadsService $service;

    public function __construct(ZohoClient $client)
    {
        $this->service = new ZohoLeadsService($client);
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
