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
    public function signup(Request $request)
    { //devolver el el token para que quede logeado
        $request->validate([
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
        ]);

        $zohoService = new ZohoController();
        $response = $zohoService->GetByEmailService('Contacts', $request["email"]);

        if ($response != null) { //A -> Esta en CRM
            if (isset($response->data) && count($response->data) > 0) { //Existe en CRM
                if ($response->data->Password != null && $response->data->Usuario != null) {

                    $user = new User([
                        'name' => $response->data->Usuario,
                        'email' => $response->data->Usuario,
                        'password' => Hash::make($response->data->Password),
                    ]);

                    $user->save();


                    return response()->json([
                        'message' => 'Successfully created user!'
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
                        "Phone" => $request->phone,
                        "usuario_prueba" => true,
                        "Caracter_stica_contacto" => "Experiencia MSK"
                    ]
                ]
            ];

            $response = $zohoService->Create('Contacts', $data);
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
                $contactCreated = $response['data'][0];

                if (isset($contactCreated['Usuario']) && isset($contactCreated['Password'])) {
                    $user = User::Create([
                        'name' => $contactCreated['Usuario'],
                        'email' => $contactCreated['Usuario'],
                        'password' => Hash::make($contactCreated['Password']),
                    ]);

                    $newContact = Contact::Create([
                        'last_name' => $contactCreated['Last_Name'],
                        'email' => $contactCreated['Usuario'],
                        'user_id' => $user->id,
                        'entity_id_crm' => $contactCreated['id']
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
                'message' => 'Credenciales incorrectas',
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
            //Dato del codigo del usuario validado, para seguridad
            'validate' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required',
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
                    "Generar_nueva_password" => 0,
                    "Password" => $request->password,
                ]
            ]
        ];
        $contact = Contact::where(["email" => $request->email])->first();

        $zohoService = new ZohoController();
        // $response = $zohoService->Update('Contacts', $data, $contact->entity_id_crm);
        $response = $zohoService->Update('Contacts', $data, "5344455000004144002");

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

    public function GetProfile(Request $request, $email)
    {
        // Me pide contaaacto tacto tacto
        $user = User::with('contact.contracts.products')->where('email', $email)->first();

        return response()->json([
            $user,
            // $user->likes
        ]);
    }
    public function RequestPasswordChange(Request $request)
    {

        $data = [
            "data" => [
                [
                    "Generar_nueva_password" => 1,
                ]
            ]
        ];
        $contact = Contact::where(["email" => $request->email])->first();

        $zohoService = new ZohoController();
        $response = $zohoService->Update('Contacts', $data, $contact->entity_id_crm);
        //$response = $zohoService->Update('Contacts', $data, "5344455000004144002");

        return response()->json([
            "message" => "Solicitud enviada.",
            $response
        ]);

    }
    public function ValidatePasswordChange(Request $request)
    {
        $contacto = Contact::where('validate', $request->validate)->first();

        if ($contacto) {
            // Si el contacto existe, muestra el formulario
            return response()->json([
                $contacto,
                "redirect" => "FormChangePassword"
            ]);
        } else {
            // Si el código no coincide con ningún contacto, muestra un error o redirecciona a otra página
            return response()->json([
                "error" => "Codigo no valido.",
            ]);
        }
    }
}