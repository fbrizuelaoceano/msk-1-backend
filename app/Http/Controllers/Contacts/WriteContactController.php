<?php

namespace App\Http\Controllers\Contacts;

use App\Clients\ZohoClient;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use App\Models\Contact;
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

    public function updateProfile(Request $request, $email): \Illuminate\Http\JsonResponse
    {
        $contactData = $request->only(Contact::getFormAttributesUpdateProfileRequest());
        $contactData['profession'] = ($contactData['profession'] === "" || $contactData['profession'] === null) ? null : $contactData['profession'];
        $contactDataForDB = $request->only(Contact::getFormAttributesPutProfile());
        $contact = Contact::where('email', $email)->first();

        try{
            $transformed = ContactsInsertTransform::handleUpdateProfile($contactData);
            $data = $this->service->put($contact->entity_id_crm, $transformed);

            $contact->update($contactDataForDB);

            return Responser::success($data);
        } catch (\Exception $e) {
            return Responser::error($e);
        }
    }
}
