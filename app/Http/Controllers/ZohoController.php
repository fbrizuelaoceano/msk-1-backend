<?php

namespace App\Http\Controllers;

use App\Providers\ZohoServiceProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
// use samples\src\com\zoho\crm\api\initializer;

<<<<<<< Updated upstream
use samples\src\com\zoho\crm\api\initializer\Initialize;

=======
// use samples\src\com\zoho\crm\api\initializer\Initialize;
>>>>>>> Stashed changes
class ZohoController extends Controller
{
    private $accessToken;

<<<<<<< Updated upstream
=======
    private $ZOHO_API_BASE_URL = '';
    private $ZOHO_CLIENT_ID = '';
    private $ZOHO_CLIENT_SECRET = '';

    private $ZOHO_GRANT_TOKEN='';
    private $ZOHO_REFRESH_TOKEN='';
    private $ZOHO_ACCESS_TOKEN='';

    private $URL_ZOHO = '';

>>>>>>> Stashed changes
    public function __construct()
    {
        try {

            $this->ZOHO_API_BASE_URL = env("ZOHO_API_BASE_URL");
            $this->ZOHO_CLIENT_ID = env("ZOHO_CLIENT_ID");
            $this->ZOHO_CLIENT_SECRET = env("ZOHO_CLIENT_SECRET");
            
            $this->ZOHO_GRANT_TOKEN=env('ZOHO_GRANT_TOKEN');
            $this->ZOHO_REFRESH_TOKEN=env('ZOHO_REFRESH_TOKEN');
            $this->ZOHO_ACCESS_TOKEN=env('ZOHO_ACCESS_TOKEN');

<<<<<<< Updated upstream
            $URL = 'https://' . $ZOHO_API_BASE_URL . '/oauth/v2/token?' .
                'refresh_token=' . $ZOHO_REFRESH_TOKEN .
                '&client_id=' . $ZOHO_CLIENT_ID .
                '&client_secret=' . $ZOHO_CLIENT_SECRET .
                '&grant_type=' . 'refresh_token';

            $response = Http::post($URL)->json();
            // Dump the contents of the variable
            var_dump($response->data->access_token);
            Storage::disk('public')->put('/zoho/access_token.txt', $response->data->access_token);

            // putenv('ZOHO_ACCESS_TOKEN='.$response->data->access_token);//no se guarda
        } catch (Exception $e) {
=======
            $this->URL_ZOHO=env('URL_ZOHO');

        }catch(Exception $e){
>>>>>>> Stashed changes
            Log::error($e);
        }
    }

    function CreateAccessToken()
    {

<<<<<<< Updated upstream
        $ZOHO_API_BASE_URL = env('ZOHO_API_BASE_URL');
        $ZOHO_CLIENT_ID = env('ZOHO_CLIENT_ID');
        $ZOHO_CLIENT_SECRET = env('ZOHO_CLIENT_SECRET');
        $ZOHO_REFRESH_TOKEN = env('ZOHO_REFRESH_TOKEN');

        $URL = 'https://' . $ZOHO_API_BASE_URL . '/oauth/v2/token?' .
            'refresh_token=' . $ZOHO_REFRESH_TOKEN .
            '&client_id=' . $ZOHO_CLIENT_ID .
            '&client_secret=' . $ZOHO_CLIENT_SECRET .
            '&grant_type=' . 'refresh_token';
=======
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
        

        $URL = 'https://'.$this->ZOHO_API_BASE_URL.'/oauth/v2/token?'.
        'refresh_token='.$this->ZOHO_REFRESH_TOKEN.
        '&client_id='.$this->ZOHO_CLIENT_ID.
        '&client_secret='.$this->ZOHO_CLIENT_SECRET.
        '&grant_type='.'refresh_token';
>>>>>>> Stashed changes

        $response = Http::post($URL)->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream

    function GetLeads()
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN'); //obtener de otro lado porque no se guarda
        $URL_ZOHO = env('URL_ZOHO') . '/Leads';

        // return response()->json([
        //     'data' => $ZOHO_ACCESS_TOKEN,
        // ]);

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
=======
  
    function GetLeadsold(){
        $URL_ZOHO = env('URL_ZOHO').'/Leads';
        
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
>>>>>>> Stashed changes
        ])
            ->get($URL_ZOHO)->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function GetByIdLeads($id)
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Leads/search?criteria=(id:equals:' . $id . ')';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
        ])
=======
    function GetByIdLeads($id){
        $URL_ZOHO = env('URL_ZOHO').'/Leads/search?criteria=(id:equals:'.$id.')';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
            ])
>>>>>>> Stashed changes
            ->get($URL_ZOHO)
            ->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function CreateLeads(Request $request)
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Leads';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
            'Content-Type' => 'application/json'
        ])
=======
    function CreateLeads(Request $request){
        $URL_ZOHO = env('URL_ZOHO').'/Leads';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
                'Content-Type' => 'application/json'
            ])
>>>>>>> Stashed changes
            ->post($URL_ZOHO, $request->all())
            ->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function UpdateLeads(Request $request, $id)
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Leads' . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
        ])
=======
    function UpdateLeads(Request $request,$id){
        $URL_ZOHO = env('URL_ZOHO').'/Leads'.'/'.$id;

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
            ])
>>>>>>> Stashed changes
            ->put($URL_ZOHO, $request->all())
            ->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function DeleteLeads(Request $request, $ids)
    {

        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Leads?ids=' . $ids . '&wf_trigger=true';
=======
    function DeleteLeads(Request $request,$ids){
        
        $URL_ZOHO = env('URL_ZOHO').'/Leads?ids='.$ids.'&wf_trigger=true';
>>>>>>> Stashed changes

        if ($ids)

<<<<<<< Updated upstream
            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
=======
        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
>>>>>>> Stashed changes
            ])
                ->delete($URL_ZOHO, $request->all())
                ->json();

        return response()->json([
            'data' => $response,
        ]);
    }

<<<<<<< Updated upstream
    function GetContacts()
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN'); //obtener de otro lado porque no se guarda
        $URL_ZOHO = env('URL_ZOHO') . '/Contacts';

        // return response()->json([
        //     'data' => $ZOHO_ACCESS_TOKEN,
        // ]);

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
=======
    function GetContacts(){
        $URL_ZOHO = env('URL_ZOHO').'/Contacts';
       
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
>>>>>>> Stashed changes
        ])
            ->get($URL_ZOHO)->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function GetByIdContacts($id)
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Contacts/search?criteria=(id:equals:' . $id . ')';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
        ])
=======
    function GetByIdContacts($id){
        $URL_ZOHO = env('URL_ZOHO').'/Contacts/search?criteria=(id:equals:'.$id.')';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
            ])
>>>>>>> Stashed changes
            ->get($URL_ZOHO)
            ->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function UpdateContacts(Request $request, $id)
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Contacts' . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
        ])
=======
    function UpdateContacts(Request $request,$id){
        $URL_ZOHO = env('URL_ZOHO').'/Contacts'.'/'.$id;

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
            ])
>>>>>>> Stashed changes
            ->put($URL_ZOHO, $request->all())
            ->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function CreateContacts(Request $request)
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Contacts';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
            'Content-Type' => 'application/json'
        ])
=======
    function CreateContacts(Request $request){
        $URL_ZOHO = env('URL_ZOHO').'/Contacts';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
                'Content-Type' => 'application/json'
            ])
>>>>>>> Stashed changes
            ->put($URL_ZOHO, $request->all())
            ->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function DeleteContacts(Request $request, $ids)
    {

        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Contacts?ids=' . $ids . '&wf_trigger=true';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
        ])
=======
    function DeleteContacts(Request $request,$ids){
        
        $URL_ZOHO = env('URL_ZOHO').'/Contacts?ids='.$ids.'&wf_trigger=true';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
            ])
>>>>>>> Stashed changes
            ->delete($URL_ZOHO, $request->all())
            ->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream

    function GetContracts()
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN'); //obtener de otro lado porque no se guarda
        $URL_ZOHO = env('URL_ZOHO') . '/Contracts';

=======
    
    function GetContracts(){
        $URL_ZOHO = env('URL_ZOHO').'/Contracts';
        
>>>>>>> Stashed changes
        // return response()->json([
        //     'data' => $ZOHO_ACCESS_TOKEN,
        // ]);

        $response = Http::withHeaders([
<<<<<<< Updated upstream
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
=======
            'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
>>>>>>> Stashed changes
        ])
            ->get($URL_ZOHO)->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function GetByIdContracts($id)
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Contracts/search?criteria=(id:equals:' . $id . ')';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
        ])
=======
    function GetByIdContracts($id){
        $URL_ZOHO = env('URL_ZOHO').'/Contracts/search?criteria=(id:equals:'.$id.')';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
            ])
>>>>>>> Stashed changes
            ->get($URL_ZOHO)
            ->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function CreateContracts(Request $request)
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Contracts';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
            'Content-Type' => 'application/json'
        ])
=======
    function CreateContracts(Request $request){
        $URL_ZOHO = env('URL_ZOHO').'/Contracts';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
                'Content-Type' => 'application/json'
            ])
>>>>>>> Stashed changes
            ->post($URL_ZOHO, $request->all())
            ->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function UpdateContracts(Request $request, $id)
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Contracts' . '/' . $id;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
        ])
=======
    function UpdateContracts(Request $request,$id){
        $URL_ZOHO = env('URL_ZOHO').'/Contracts'.'/'.$id;

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
            ])
>>>>>>> Stashed changes
            ->put($URL_ZOHO, $request->all())
            ->json();

        return response()->json([
            'data' => $response,
        ]);
    }
<<<<<<< Updated upstream
    function DeleteContracts(Request $request, $ids)
    {

        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Contracts?ids=' . $ids . '&wf_trigger=true';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
        ])
=======
    function DeleteContracts(Request $request,$ids){
        
        $URL_ZOHO = env('URL_ZOHO').'/Contracts?ids='.$ids.'&wf_trigger=true';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
            ])
>>>>>>> Stashed changes
            ->delete($URL_ZOHO, $request->all())
            ->json();

        return response()->json([
            'data' => $response,
        ]);
    }

<<<<<<< Updated upstream
    function ConvertLead(Request $request, $id)
    {
        $ZOHO_ACCESS_TOKEN = env('ZOHO_ACCESS_TOKEN');
        $URL_ZOHO = env('URL_ZOHO') . '/Leads' . '/' . $id . '/actions/convert';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $ZOHO_ACCESS_TOKEN,
            'Content-Type' => 'application/json',
        ])
            ->post($URL_ZOHO, $request->all())
            ->json();

=======
    function ConvertLead(Request $request,$id){
        $URL_ZOHO = env('URL_ZOHO').'/Leads'.'/'.$id.'/actions/convert';

        $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
                'Content-Type' => 'application/json',
            ])
            ->post($URL_ZOHO, $request->all())
            ->json();
    
        return response()->json([
            'data' => $response,
        ]);
    }
    function GetByEmail($module,$email){
        $URL_ZOHO = env('URL_ZOHO').'/'.$module.'/search?email='.$email;

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken '.$this->ZOHO_ACCESS_TOKEN,
        ])
        ->get($URL_ZOHO)->json();
    
>>>>>>> Stashed changes
        return response()->json([
            'data' => $response,
        ]);
    }


    function prueba()
    {
        $accessToken = Storage::disk('public')->get('/zoho/access_token.txt');

        return response()->json([
            'data' => $accessToken,
        ]);
    }
}