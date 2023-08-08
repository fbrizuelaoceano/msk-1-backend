<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Contact;
use App\Models\Profession;
use App\Models\Speciality;
use App\Services\ZohoCRMService;

class AuthController extends Controller
{
    private $zohoService;

    public function __construct(ZohoCRMService $service)
    {
        $this->zohoService = $service;
    }

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
            'name' => $contactObj->name,
            'last_name' => $contactObj->Last_Name,
            'email' => $request->email,
            'user_id' => $user->id,
            'entity_id_crm' => $contactObj->id,
            'phone' => $contactObj->phone
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
        ], [
            'last_name.required' => 'El campo Apellido es obligatorio.',
            'last_name.string' => 'El campo Apellido debe ser una cadena de caracteres.',
            'email.required' => 'El campo Email es obligatorio.',
            'email.email' => 'El campo Email debe ser una direccion de correo electronico valida.',
            'email.unique' => 'El correo electronico ya ha sido registrado.',
        ]);

        // $zohoService = new ZohoCRMService();
        $response = $this->zohoService->GetByEmailService('Contacts', $request["email"]);

        if ($response != null) { //A -> Esta en CRM
            if (isset($response->data) && count($response->data) > 0) { //Existe en CRM
                if ($response->data->Password != null && $response->data->Usuario != null) {

                    $user = new User([
                        'name' => $response->data->Usuario,
                        'email' => $response->data->Usuario,
                        'password' => Hash::make($response->data->Password),
                    ]);

                    $user->save();

                    //aca se podria agregar que cree al contacto si no lo tiene. Ver si no es un errorde vulnerabiliad.
                    $newContact = Contact::updateOrCreate(
                        [
                            'email' => $response['Usuario'],
                        ],
                        [
                            'name' => $response['First_Name'],
                            'phone' => $response['Phone'],
                            'last_name' => $response['Last_Name'],
                            'email' => $response['Usuario'],
                            'user_id' => $user->id,
                            'entity_id_crm' => $response['id'],
                            'country' => $response['Pais'],
                            "profession" => $response['Profesi_n'],
                            "speciality" => $response['Especialidad'],
                            'other_profession' => $response['Otra_profesi_n'],
                            'other_speciality' => $response['Otra_especialidad'],

                        ]
                    );

                    //No le podes devolver un token de sesion porque yo podria poner cualquier email y la pagina me estaria dejando logearme desde la creacion de usuario. Por eso le digo que revise su email o el contacto en crm para logearse.
                    return response()->json([
                        'message' => 'El usuario ya esta registradoba en CRM. Le actualizamos la informacion en nuestra base de datos, intente nuevamente. '
                    ], 201);
                }
            } else {
               Log::info("if ($response != null) { //A -> Esta en CRM: ".print_r($response, true));

                if(isset($response->data[0])){
                    $contact = $response->data[0];
                    $user = User::createOrUpdate(
                        [
                            'email' => $contact['Usuario'],
                        ],
                        [
                            'name' => $contact['Usuario'],
                            'email' => $contact['Usuario'],
                            'password' => Hash::make($contact['Password']),
                        ]
                    );
                    $newContact = Contact::createOrUpdate(
                        [
                            'email' => $contact['Usuario'],
                        ],
                        [
                            'name' => $contact['First_Name'],
                            'phone' => $contact['Phone'],
                            'last_name' => $contact['Last_Name'],
                            'email' => $contact['Usuario'],
                            'user_id' => $user->id,
                            'entity_id_crm' => $contact['id'],
                            'country' => $contact['Pais'],
                            "profession" => $contact['Profesi_n'],
                            "speciality" => $contact['Especialidad'],
                            'other_profession' => $contact['Otra_profesi_n'],
                            'other_speciality' => $contact['Otra_especialidad'],
                        ]
                    );
                }
                

                return response()->json([
                    'message' => "El usuario ya estaba registrado en CRM. Revise sus emails para validar su usuario y contraseña. Verifique que db de msk tenga su usuario y contacto",
                    'responseCRM' => $response
                ]);
            }
        } else { //B -> No esta en CRM
            $data = [
                "data" => [
                    [
                        "Last_Name" => $request->last_name,
                        "Email" => $request->email,
                        "First_Name" => $request->first_name,
                        "Phone" => $request->phone,
                        "usuario_prueba" => env("APP_DEBUG"),
                        "Caracter_stica_contacto" => "Experiencia MSK",
                        "Pais" => $request->country,
                    
                        "Especialidad" => isset($request->Especialidad) ? $request->Especialidad : null, 
                        "Profesi_n" => isset($request->Otra_profesi_n) ? $request->Profesi_n : null,
                                            
                        "Otra_especialidad" => isset($request->Otra_especialidad) ? $request->Otra_especialidad : null, 
                        "Otra_profesi_n" => isset($request->Otra_profesi_n) ? $request->Otra_profesi_n : null
                    ]
                ]
            ];

            $response = $this->zohoService->Create('Contacts', $data);
            /*Al crear usuario en crm productivo se ejecuta un flow que crea user y password.
            Despues de craer los usuarios llama a la api msk productivo para hacer el registro de usuario en la base de msk
            Con esto, ej:
            result2 = invokeurl
            [
                url : "https://msklatam.com/msk-laravel/public/api/signupForCRM"
                type : POST
                parameters : new
            ];
            */
            // return response()->json($response,);
            // // Validar si se creo o no
            // Cuando se cree el contacto.
            if (isset($response['data'][0]['code']) && $response['data'][0]['code'] == "SUCCESS") {
                $response = $this->zohoService->GetById('Contacts', $response['data'][0]['details']['id']);
                $contactCreated = $response['data'][0];

                if (isset($contactCreated['Usuario']) && isset($contactCreated['Password'])) {
                    $user = User::Create([
                        'name' => $contactCreated['Usuario'],
                        'email' => $contactCreated['Usuario'],
                        'password' => Hash::make($contactCreated['Password']),
                    ]);

                    $newContact = Contact::Create([

                        'name' => $contactCreated['First_Name'],
                        'phone' => $contactCreated['Phone'],
                        'last_name' => $contactCreated['Last_Name'],
                        'email' => $contactCreated['Usuario'],
                        'user_id' => $user->id,
                        'entity_id_crm' => $contactCreated['id'],
                        'country' => $contactCreated['Pais'],
                        "profession" => $contactCreated['Profesi_n'],
                        "speciality" => $contactCreated['Especialidad'],
                        'other_profession' => $contactCreated['Otra_profesi_n'],
                        'other_speciality' => $contactCreated['Otra_especialidad'],
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
                    'message' => 'Error al crear el usuario en ZohoCRM',
                    'resposneCRM' => $response,
                ], 500);
            }
        }
    }

    /**
     * Login user and create token.
     *
     * @param  \Illuminate\Http\Request  $request
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

        $response = $this->zohoService->Update('Contacts', $data, $contact->entity_id_crm);
        //$response = $zohoService->Update('Contacts', $data, "5344455000004144002");

        return response()->json($response, 201);
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
        try {
            Log::info("GetProfile-email: " . print_r($email, true));

            $user = User::with('contact.contracts.products', 'contact.courses_progress')
                ->where('email', $email)
                ->first();

            Log::info("GetProfile-user: " . print_r($user, true));

            $contracts = $user->contact->contracts;
            Log::info("GetProfile-contracts: " . print_r($contracts, true));

            $contracts->each(function ($contract) {
                $contract->setAttribute('products', $contract->products);
            });

            Log::info("GetProfile-user2: " . print_r($user, true));


            return response()->json([
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ];

            Log::error("Error en GetProfile: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));

            return response()->json([
                'error' => 'Ocurrió un error en el servidor',
                $err,
            ], 500);
        }
    }

    // public function PutProfile(Request $request, $email)
    public function PutProfile(UpdateProfileRequest $request, $email)
    {
        try {

            // $contactData = $request->only(['name', 'last_name','email','phone','profession','other_profession', 'speciality', 'other_speciality','address', 'country','state','postal_code','rfc','fiscal_regime']);
            $contactData = $request->only(UpdateProfileRequest::$formAttributes); //pasar el formAttributes al contacto

            $data = [
                'data' => [
                    [
                        'First_Name' => $contactData['name'],
                        'Last_Name' => $contactData['last_name'],
                        'Email' => $contactData['email'],
                        'Usuario' => $contactData['email'],
                        'Phone' => $contactData['phone'],
                        'Profesi_n' => $contactData['profession'],
                        'Otra_profesi_n' => $contactData['other_profession'],
                        'Especialidad' => $contactData['speciality'],
                        'Otra_especialidad' => $contactData['other_speciality'],
                        'Pais' => $contactData['country'],
                        'Mailing_State' => $contactData['state'],
                        'Mailing_Zip' => $contactData['postal_code'],

                        'RFC' => $contactData['rfc'],
                        // Mexico
                        // 'RUT' => $contactData['rut'],// Chile
                        // 'No-definido' => $contactData['mui'],// Ecuador. Cual es el campo en crm ?
                        // 'CUIT_CUIL_o_DNI' => $contactData['dni'], // Argentina

                        'R_gimen_fiscal' => $contactData['fiscal_regime'],
                        'Mailing_Street' => $contactData['address'],
                    ]
                ]
            ];

            $response = $this->zohoService->Update('Contacts', $data, $request->entity_id_crm);

            return response()->json([
                'updateCRM' => $response
            ]);
        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ];

            Log::error("Error en PutProfile: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));

            return response()->json([
                'error' => 'Ocurrió un error en el servidor',
                $err,
            ], 500);
        }
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

        $response = $this->zohoService->Update('Contacts', $data, $contact->entity_id_crm);
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
                "contact" => $contacto,
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