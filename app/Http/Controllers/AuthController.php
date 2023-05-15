<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Contact;

class AuthController extends Controller
{

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function signupForCRM(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string',
        ]);

        $contact = collect($_POST['contact'])->toArray()[0];
        $contactObj = json_decode($contact)[0];

        $user = User::updateOrCreate(['email' => $request->email], [
            'name' => $contactObj->First_Name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        Contact::updateOrCreate(['email' => $request->email], [
            'last_name' => $contactObj->Last_Name,
            'email' => $request->email,
            'user_id' => $user->id,
            'entity_id_crm' => $contactObj->id
        ]);


        // Crea un nuevo token de acceso
        $tokenResult = $user->createToken($request->email);
        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'message' => 'Successfully created user!',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->expires_at,
        ], 201);
    }

    /**
     * Login user and create token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'ANDA PA ALLA BOBO',
            ], 401);
        }

        $user = $request->user();
        // Revoca todos los tokens activos del usuario
        $user->tokens()->where('revoked', false)->update(['revoked' => true]);
        // Crea un nuevo token de acceso
        $tokenResult = $user->createToken($request->email);
        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->expires_at,
        ]);
    }

    /**
     * Logout user (Revoke the token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->where('revoked', false)->update(['revoked' => true]);
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
    public function newPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|confirmed',
        ]);

        $user = User::where(["email" => $request->email])->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Revoca todos los tokens activos del usuario
        $user->tokens()->where('revoked', false)->update(['revoked' => true]);
        // Crea un nuevo token de acceso
        $tokenResult = $user->createToken($request->email);
        $token = $tokenResult->token;

        $token->save();

        $data = [
            "data" => [
                [
                    "Password" => $request->password,
                ]
            ]
        ];
        $contact = Contact::where(["email" => $request->email])->first();

        $zohoService = new ZohoController();
        $response = $zohoService->Update('Contacts', $data, $contact->entity_id_crm);

        // $URL_ZOHO = env('URL_ZOHO') . '/Contacts' . '/' . $contact->entity_id_crm;
        // $response = Http::withHeaders([
        //     'Authorization' => 'Zoho-oauthtoken ' . env("ZOHO_ACCESS_TOKEN"),
        // ])
        //     ->put($URL_ZOHO, $data)
        //     ->json();

        return response()->json([
            'message' => 'Successfully created user!',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->expires_at,
        ], 201);
    }
    /**
     * Get the authenticated User.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function signup(Request $request)
    { //devolver el el token para que quede logeado

        $request->validate([
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            // 'password' => 'required|string',
        ]);

        $zohoService = new ZohoController();
        $response = $zohoService->GetByEmailService('Contacts', $request["email"]);

        // $URL_ZOHO = env('URL_ZOHO').'/Contacts/search?email='.$request["email"];
        // $response = Http::withHeaders([
        //     'Authorization' => 'Zoho-oauthtoken '.env("ZOHO_ACCESS_TOKEN"),
        // ])
        // ->get($URL_ZOHO)->json();

        if ($response != null) { //A -> Esta en CRM
            if (isset($response->data) && count($response->data) > 0) { //Existe en CRM
                if ($response->data->Password != null && $response->data->Usuario != null) {
                    $user = new User([
                        'name' => $response->data->Usuario,
                        'email' => $response->data->Usuario,
                        'password' => Hash::make($response->data->Password),
                    ]);
                    $user->save();
                    // Crea un nuevo token de acceso
                    $tokenResult = $user->createToken($request->email);
                    $token = $tokenResult->token;
                    $token->save();

                    return response()->json([
                        'message' => 'Successfully created user!',
                        'access_token' => $tokenResult->accessToken,
                    ], 201);
                }
            } else {
                return response()->json([
                    'message' => "Error consultar por email en api CRM",
                    'responseCRM' => $response
                ]);
            }
        } else { //B -> No esta en CRM

            $data = [
                "data" => [
                    [
                        "Last_Name" => $request->last_name,
                        "Email" => $request->email,
                        "First_Name" => $request->name,
                        // "Password" => $request->password,
                        "Phone" => $request->phone,
                        "usuario_prueba" => true
                    ]
                ]
            ];

            $response = $zohoService->Create('Contacts', $data);
            $URL_ZOHO = env('URL_ZOHO') . '/Contacts';

            // $response = Http::withHeaders([
            //         'Authorization' => 'Zoho-oauthtoken '.env("ZOHO_ACCESS_TOKEN"),
            //         'Content-Type' => 'application/json'
            //     ])
            // ->post($URL_ZOHO, $data )
            // ->json();

            /*Al crear usuario en crm productivo se ejecuta un flow que crea user y password.
            Despues de craer los usuarios llama a la api msk productivo para hacer el registro de usuario en la base de msk
            Con esto, ej:
            result2 = invokeurl
            [
            url :"https://msklatam.com/msk-laravel/public/api/signupForCRM"
            type :POST
            parameters:new
            ];
            */

            // return response()->json($response,);

            // // Validar si se creo o no

            // Cuando se cree el contacto.
            if (isset($response['data'][0]['code']) && $response['data'][0]['code'] == "SUCCESS") {

                $response = $zohoService->GetById('Contacts', $response['data'][0]['details']['id']);

                // $URL_ZOHO = env('URL_ZOHO').'/Contacts/search?criteria=(id:equals:'.$response['data'][0]['details']['id'].')';
                // $response = Http::withHeaders([
                //         'Authorization' => 'Zoho-oauthtoken '.env("ZOHO_ACCESS_TOKEN"),
                //     ])
                //     ->get($URL_ZOHO)
                //     ->json();

                if (isset($response['data'][0]['Usuario']) && isset($response['data'][0]['Password'])) {

                    $user = User::Create([
                        'name' => $response['data'][0]['Usuario'],
                        'email' => $response['data'][0]['Usuario'],
                        'password' => Hash::make($response['data'][0]['Password']),
                    ]);

                    $newContact = Contact::Create([
                        'last_name' => $response['data'][0]['Last_Name'],
                        'email' => $response['data'][0]['Usuario'],
                        'user_id' => $user->id,
                        'entity_id_crm' => $response['data'][0]['id']
                    ]);

                    // Revoca todos los tokens activos del usuario
                    $user->tokens()->where('revoked', false)->update(['revoked' => true]);
                    // Crea un nuevo token de acceso
                    $tokenResult = $user->createToken($request->email);
                    $token = $tokenResult->token;

                    $token->save();

                    return response()->json([
                        'message' => 'Successfully created user!',
                        'access_token' => $tokenResult->accessToken,
                        'token_type' => 'Bearer',
                        'expires_at' => $token->expires_at,
                    ], 201);
                }
            } else {
                return response()->json([
                    'message' => 'Error al crear el usuario en ZhoCRM',
                    'resposneCRM' => $response,

                ], 201);
            }

        }

    }

    public function CreateContact(Request $request)
    {
        $newOrUpdatedLead = Contact::Create([
            'last_name' => $request->email,
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Successfully created user!',
        ], 201);
    }
    
    public function GetProfile(Request $request, $email) {
        $user = User::where("email", $email)->first();
        return response()->json([$user]);
    }
}