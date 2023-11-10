<?php

namespace App\Http\Controllers\Leads;

use App\Clients\ZohoClient;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use App\Services\ZohoLeadsService;
use Illuminate\Http\Request;
use App\Transformers\LeadsInsertTransform;

class WriteLeadController extends Controller
{
    private ZohoLeadsService $service;

    public function __construct(ZohoClient $client)
    {
        $this->service = new ZohoLeadsService($client);
    }
    
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try{
            $transformed = LeadsInsertTransform::handle($request->all());
            $data = $this->service->post($transformed);

            return Responser::success($data);
        } catch (\Exception $e) {
            return Responser::error($e);
        }
    }
}
