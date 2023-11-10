<?php

namespace App\Http\Controllers\Users;

use App\Clients\ZohoClient;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use App\Services\ZohoUsersService;

class ReadUserController extends Controller
{
    private ZohoUsersService $service;

    public function __construct(ZohoClient $client)
    {
        $this->service = new ZohoUsersService($client);
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
