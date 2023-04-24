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

        $contacts = Contact::create();

        return response()->json([
            'contact' => $contacts,
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
