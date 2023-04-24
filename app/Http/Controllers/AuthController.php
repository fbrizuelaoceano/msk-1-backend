<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Contact;

use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function newPassword(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|confirmed',
        ]);

        $user = User::where([ "email" => $request->email])->first();
        $user->password = Hash::make($request->password);

        $activeTokens = $user->tokens()->where('revoked', false)->where('expires_at', '>', now())->get();
        if ($activeTokens->count() > 0) {
            $token = $activeTokens->first();
        } else {
            // Genera un nuevo token de acceso
            $tokenResult = $user->createToken($request->email.'-Personal Access Token');
            $token = $tokenResult->token;
        }

        if ($request->remember_me) {
            $token->expires_at = now()->addWeek(1);
        }

        $token->save();

        return response()->json([
            'message' => 'Successfully created user!',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->expires_at,
        ], 201);  
    }
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function signupForCRM(Request $request)
    {
        $request->validate([
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
        ]);
        
        $user = new User([
            'name' => $request->email,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->save();

        $newOrUpdatedLead = Contact::Create([
            'last_name' => $request->last_name,
            'email' => $request->email,
            'user_id' => $user->id
        ]);

        $activeTokens = $user->tokens()->where('revoked', false)->where('expires_at', '>', now())->get();
        if ($activeTokens->count() > 0) {
            $token = $activeTokens->first();
        } else {
            // Genera un nuevo token de acceso
            $tokenResult = $user->createToken($request->email.'-Personal Access Token');
            $token = $tokenResult->token;
        }

        if ($request->remember_me) {
            $token->expires_at = now()->addWeek(1);
        }

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
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = $request->user();
        // Verifica si el usuario tiene tokens activos
        $activeTokens = $user->tokens()->where('revoked', false)->where('expires_at', '>', now())->get();
        if ($activeTokens->count() > 0) {
            $token = $activeTokens->first();
        } else {
            // Genera un nuevo token de acceso
            $tokenResult = $user->createToken($request->email.'-Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me) {
                $token->expires_at = now()->addMinutes(120);
            }
            $token->save();
        }

        if ($request->remember_me) {
            $token->expires_at = now()->addWeek(1);
        }

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
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
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
    
    public function signup(Request $request){//devolver el el token para que quede logeado
        
        $request->validate([
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            // 'password' => 'required|string',
        ]);
        
        $URL_ZOHO = env('URL_ZOHO').'/Contacts/search?email='.$request["email"];
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken '.env("ZOHO_ACCESS_TOKEN"),
        ])
        ->get($URL_ZOHO)->json();

        if ($response != null ) {//A -> Esta en CRM
            if(isset($response->data) && count($response->data) > 0){//Existe en CRM
                if($response->data->Password != null && $response->data->Usuario != null ){
                    $user = new User([
                        'name' => $response->data->Usuario,
                        'email' => $response->data->Usuario,
                        'password' => Hash::make($response->data->Password),
                    ]);
            
                    $user->save();

                    return response()->json([
                        'message' => 'Successfully created user!',
                    ], 201);
                }
            }else{
                return response()->json([
                    'message' => "Error consultar por email en api CRM",
                    'responseCRM' => $response
                ]);
            }
        }else{//B -> No esta en CRM

            $data = [
                "data" => [
                    [
                        "Last_Name" => $request->last_name,
                        "Email" => $request->email
                    ]
                ]
            ];

            $URL_ZOHO = env('URL_ZOHO').'/Contacts';
            $response = Http::withHeaders([
                    'Authorization' => 'Zoho-oauthtoken '.env("ZOHO_ACCESS_TOKEN"),
                    'Content-Type' => 'application/json'
                ])
            ->post($URL_ZOHO, $data )
            ->json();

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
            if(isset($response['data'][0]['code']) && $response['data'][0]['code'] == "SUCCESS"){

                $URL_ZOHO = env('URL_ZOHO').'/Contacts/search?criteria=(id:equals:'.$response['data'][0]['details']['id'].')';
                $response = Http::withHeaders([
                        'Authorization' => 'Zoho-oauthtoken '.env("ZOHO_ACCESS_TOKEN"),
                    ])
                    ->get($URL_ZOHO)
                    ->json();
                
                if(isset($response['data'][0]['Usuario']) && isset($response['data'][0]['Password'])){
                    
                    $user = new User([
                        'name' => $response['data'][0]['Usuario'],
                        'email' => $response['data'][0]['Usuario'],
                        'password' => Hash::make($response['data'][0]['Password']),
                    ]);
                
                    $user->save();
                
                    $newOrUpdatedLead = Contact::Create([
                        'last_name' => $response['data'][0]['Last_Name'],
                        'email' => $response['data'][0]['Usuario'],
                        'user_id' => $user->id
                    ]);

                    $activeTokens = $user->tokens()->where('revoked', false)->where('expires_at', '>', now())->get();
                    if ($activeTokens->count() > 0) {
                        $token = $activeTokens->first();
                    } else {
                        // Genera un nuevo token de acceso
                        $tokenResult = $user->createToken($request->email.'-Personal Access Token');
                        $token = $tokenResult->token;
                    }
            
                    if ($request->remember_me) {
                        $token->expires_at = now()->addWeek(1);
                    }
            
                    $token->save();
    
                    return response()->json([
                        'message' => 'Successfully created user!',
                        'access_token' => $tokenResult->accessToken,
                        'token_type' => 'Bearer',
                        'expires_at' => $token->expires_at,
                    ], 201);
                }
            }else{
                return response()->json([
                    'message' => 'Error al crear el usuario en ZhoCRM',
                    'resposneCRM' => $response,
                    
                ], 201);
            }
           
        }

    }

    public function CreateContact(Request $request){

        $newOrUpdatedLead = Contact::Create([
            'last_name' => $request->email,
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Successfully created user!',
        ], 201);
    }
}