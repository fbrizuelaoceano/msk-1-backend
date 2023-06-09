<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    //
    public function Contacts(){

        $contacts = Contact::with('user')->get();

        return response()->json([
            'contacts' => $contacts,
        ]);
    }
    //
    public function Contact(Request $request){
        $user = User::Create([
            'name' => $request->user->name,
            'email' => $request->user->email,
            'password' => $request->user->password,
        ]);
        $newContact = Contact::Create([
            'name' => $request->contact->name,
            'Profes_n' => $request->contact->Profes_n,
            'Especialidad' => $request->contact->Especialidad,
            'RFC' => $request->contact->RFC,
            'R_gimen_fiscal' => $request->contact->R_gimen_fiscal,
            'phone' => $request->contact->phone,
            'last_name' => $request->contact->last_name,
            'email' => $request->contact->email,
            'entity_id_crm' => $request->contact->entity_id_crm,
            'dni' => $request->contact->dni,
            'sex' => $request->contact->sex,
            'date_of_birth' => $request->contact->date_of_birth,
            'registration_number' => $request->contact->registration_number,
            'area_of_work' => $request->contact->area_of_work,
            'training_interest' => $request->contact->training_interest,
            'type_of_address' => $request->contact->type_of_address,
            'country' => $request->contact->country,
            'postal_code' => $request->contact->postal_code,
            'street' => $request->contact->street,
            'locality' => $request->contact->locality,
            'province_state' => $request->contact->province_state,
        ]);

        return response()->json([
            $newContact
        ]);
            
        $contacts = Contact::create();

        return response()->json([
            'contact' => $contacts,
        ]);
    }
    public function ContactById(Request $request,$id){

        $contact = Contact::find($id);

        return response()->json([
            'contact' => $contact,
        ]);
    }
    public function relacionarUserContact(){

        $contact = Contact::all()->first();
        $user = User::all()->first();

        $contact->user_id = $user->id;

        $newOrUpdatedContact = Contact::updateOrCreate([
            'email' => $contact["email"]
        ], $contact->toArray());

        return response()->json([
            'contacts' => $newOrUpdatedContact,
        ]);

    }
}
