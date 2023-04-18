<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
// use samples\src\com\zoho\crm\api\initializer;

use samples\src\com\zoho\crm\api\initializer\Initialize;
class ZohoController extends Controller
{
    private $accessToken;

   
    

    public function __construct()
    {
        try {
            // Initialize::initialize();

            $ZOHO_API_BASE_URL = env('ZOHO_API_BASE_URL');
            $ZOHO_CLIENT_ID = env('ZOHO_CLIENT_ID');
            $ZOHO_CLIENT_SECRET = env('ZOHO_CLIENT_SECRET');
            $ZOHO_REFRESH_TOKEN = env('ZOHO_REFRESH_TOKEN');

            $URL = 'https://'.$ZOHO_API_BASE_URL.'/oauth/v2/token?'.
            'refresh_token='.$ZOHO_REFRESH_TOKEN.
            '&client_id='.$ZOHO_CLIENT_ID.
            '&client_secret='.$ZOHO_CLIENT_SECRET.
            '&grant_type='.'refresh_token';

            $response = Http::post($URL)->json();
            // Dump the contents of the variable
            var_dump($response->data->access_token);
            Storage::disk('public')->put('/zoho/access_token.txt', $response->data->access_token);

            // putenv('ZOHO_ACCESS_TOKEN='.$response->data->access_token);//no se guarda
        }catch(Exception $e){
            Log::error($e);
        }
    }

    function CreateRefreshToken(){

        $ZOHO_CLIENT_ID = env('ZOHO_CLIENT_ID');
        $ZOHO_CLIENT_SECRET = env('ZOHO_CLIENT_SECRET');
        $ZOHO_GRANT_TOKEN = env('ZOHO_GRANT_TOKEN');
        $ZOHO_API_TOKEN_URL= env('ZOHO_API_TOKEN_URL');
        $ZOHO_REDIRECT_URI=env("ZOHO_REDIRECT_URI");

        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->post($ZOHO_API_TOKEN_URL, [
            'body' => 
            'code='.$ZOHO_GRANT_TOKEN
            .'&redirect_url='.$ZOHO_REDIRECT_URI
            .'&client_id='.$ZOHO_CLIENT_ID
            .'&client_secret='.$ZOHO_CLIENT_SECRET
            .'&grant_type=authorization_code'
        ])->json();

        return response()->json([
            'data' => $response,
        ]);
    }
    function CreateAccessToken(){
        
        $ZOHO_API_BASE_URL = env('ZOHO_API_BASE_URL');
        $ZOHO_CLIENT_ID = env('ZOHO_CLIENT_ID');
        $ZOHO_CLIENT_SECRET = env('ZOHO_CLIENT_SECRET');
        $ZOHO_REFRESH_TOKEN = env('ZOHO_REFRESH_TOKEN');

        $URL = 'https://'.$ZOHO_API_BASE_URL.'/oauth/v2/token?'.
        'refresh_token='.$ZOHO_REFRESH_TOKEN.
        '&client_id='.$ZOHO_CLIENT_ID.
        '&client_secret='.$ZOHO_CLIENT_SECRET.
        '&grant_type='.'refresh_token';

        $response = Http::post($URL)->json();

        return response()->json([
            'data' => $response,
        ]);
    }
    
    function GetLeads(){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN'); //obtener de otro lado porque no se guarda
        $URL_ZOHO = env('URL_ZOHO').'/Leads';
        
        // return response()->json([
        //     'data' => $ZOHO_ACCESS_TOKEN,
        // ]);
       
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
        ])
        ->get($URL_ZOHO)->json();

        return response()->json([
            'data' => $response,
        ]);
    }
    function GetByIdLeads($id){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Leads/search?criteria=(id:equals:'.$id.')';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
            ])
            ->get($URL_ZOHO)
            ->json();
    
        return response()->json([
            'data' => $response,
        ]);
    }
    function CreateLeads(Request $request){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Leads';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
                'Content-Type' => 'application/json'
            ])
            ->post($URL_ZOHO, $request->all())
            ->json();
    
        return response()->json([
                'data' => $response,
            ]);
    }
    function UpdateLeads(Request $request,$id){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Leads'.'/'.$id;

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
            ])
            ->put($URL_ZOHO, $request->all())
            ->json();
    
        return response()->json([
                'data' => $response,
            ]);
    }
    function DeleteLeads(Request $request,$ids){
        
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Leads?ids='.$ids.'&wf_trigger=true';

        if($ids)

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
            ])
            ->delete($URL_ZOHO, $request->all())
            ->json();
    
        return response()->json([
                'data' => $response,
            ]);
    }

    function GetContacts(){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN'); //obtener de otro lado porque no se guarda
        $URL_ZOHO = env('URL_ZOHO').'/Contacts';
        
        // return response()->json([
        //     'data' => $ZOHO_ACCESS_TOKEN,
        // ]);
       
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
        ])
        ->get($URL_ZOHO)->json();

        return response()->json([
            'data' => $response,
        ]);
    }
    function GetByIdContacts($id){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Contacts/search?criteria=(id:equals:'.$id.')';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
            ])
            ->get($URL_ZOHO)
            ->json();
    
        return response()->json([
            'data' => $response,
        ]);
    }
    function UpdateContacts(Request $request,$id){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Contacts'.'/'.$id;

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
            ])
            ->put($URL_ZOHO, $request->all())
            ->json();
    
        return response()->json([
                'data' => $response,
            ]);
    }
    function CreateContacts(Request $request){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Contacts';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
                'Content-Type' => 'application/json'
            ])
            ->put($URL_ZOHO, $request->all())
            ->json();
    
        return response()->json([
                'data' => $response,
            ]);
    }
    function DeleteContacts(Request $request,$ids){
        
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Contacts?ids='.$ids.'&wf_trigger=true';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
            ])
            ->delete($URL_ZOHO, $request->all())
            ->json();
    
        return response()->json([
            'data' => $response,
        ]);
    }
    
    function GetContracts(){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN'); //obtener de otro lado porque no se guarda
        $URL_ZOHO = env('URL_ZOHO').'/Contracts';
        
        // return response()->json([
        //     'data' => $ZOHO_ACCESS_TOKEN,
        // ]);
       
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
        ])
        ->get($URL_ZOHO)->json();

        return response()->json([
            'data' => $response,
        ]);
    }
    function GetByIdContracts($id){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Contracts/search?criteria=(id:equals:'.$id.')';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
            ])
            ->get($URL_ZOHO)
            ->json();
    
        return response()->json([
            'data' => $response,
        ]);
    }
    function CreateContracts(Request $request){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Contracts';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
                'Content-Type' => 'application/json'
            ])
            ->post($URL_ZOHO, $request->all())
            ->json();
    
        return response()->json([
                'data' => $response,
            ]);
    }
    function UpdateContracts(Request $request,$id){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Contracts'.'/'.$id;

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
            ])
            ->put($URL_ZOHO, $request->all())
            ->json();
    
        return response()->json([
                'data' => $response,
            ]);
    }
    function DeleteContracts(Request $request,$ids){
        
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Contracts?ids='.$ids.'&wf_trigger=true';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
            ])
            ->delete($URL_ZOHO, $request->all())
            ->json();
    
        return response()->json([
            'data' => $response,
        ]);
    }

    function ConvertLead(Request $request,$id){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/Leads'.'/'.$id.'/actions/convert';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
                'Content-Type' => 'application/json',
            ])
            ->post($URL_ZOHO, $request->all())
            ->json();
    
        return response()->json([
            'data' => $response,
        ]);
    }
    
    function GetByEmail($module,$email){
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO').'/'.$module.'/search?email='.$email;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken '.$ZOHO_ACCESS_TOKEN,
        ])
        ->get($URL_ZOHO)->json();
    
        return response()->json([
            'data' => $response,
        ]);
    }

   function prueba(){

    $accessToken2 = Storage::disk('public')->get('/zoho/access_token.txt');
    $accessToken = Storage::disk('public')->get('/zoho/access_token.txt');

    return response()->json([
        'data' => $accessToken,
    ]);
   }
}
