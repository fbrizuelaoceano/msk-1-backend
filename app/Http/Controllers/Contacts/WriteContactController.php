<?php

namespace App\Http\Controllers\Contacts;

use App\Clients\ZohoClient;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use App\Services\ZohoContactsService;
use App\Transformers\ContactsInsertTransform;
use Illuminate\Http\Request;

class WriteContactController extends Controller
{
    private ZohoContactsService $service;

    public function __construct(ZohoClient $client)
    {
        $this->service = new ZohoContactsService($client);
    }
    
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try{
            $transformed = ContactsInsertTransform::handle($request->all());
            $data = $this->service->post($transformed);

            return Responser::success($data);
        } catch (\Exception $e) {
            return Responser::error($e);
        }
    }
}
