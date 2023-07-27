<?php

// use Illuminate\Http\Request;
use App\Http\Controllers\SSOController;
use App\Http\Controllers\Webhooks\RebillController;
use App\Http\Controllers\ZohoOMController;
use App\Http\Controllers\ZohoController;
use App\Http\Controllers\ZohoWorkflowController;
use App\Models\Profession;
use App\Models\Speciality;
use App\Models\TopicInterest;
use App\Models\ProfessionSpeciality;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseProgressController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CountryController;


Route::post('signup', [AuthController::class, 'signup']);
Route::post('signupForCRM', [AuthController::class, 'signupForCRM']);
Route::post('salesForCRM', [ZohoWorkflowController::class, 'salesForCRM']);
Route::post('setNewPasswordFromMSK', [ZohoWorkflowController::class, 'setNewPasswordFromMSK']);
Route::post('/ValidatedUser', [ZohoWorkflowController::class, 'ValidatedUser']);

Route::prefix('ZohoWorkFlow')->group(function () {
    // Usar el prefix para el zo (reglas de trabajo)
    Route::post('UpdateQuotes', [ZohoWorkflowController::class, 'UpdateQuotes']);
    Route::post('UpdateContact', [ZohoWorkflowController::class, 'UpdateContact']);

});

Route::get('GetByIdAllDetails/{module}/{id}', [ZohoController::class, 'GetByIdAllDetails']);
Route::get('GetCursadas/{id}', [ZohoController::class, 'GetCursadaService']);
Route::get('GetByEmail/{module}/{email}', [ZohoController::class, 'GetByEmailService']);
Route::get('/GetQuotes', [ZohoController::class, 'GetQuotes']);

Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('user', [AuthController::class, 'user'])->middleware('auth:api');
Route::get('/profile/{email}', [AuthController::class, 'GetProfile']);
Route::put('/profile/{email}', [AuthController::class, 'PutProfile']);
Route::post('/ValidatePasswordChange', [AuthController::class, 'ValidatePasswordChange']);
Route::post('/RequestPasswordChange', [AuthController::class, 'RequestPasswordChange']);
Route::post('/newPassword', [AuthController::class, 'newPassword']);

Route::get('prueba', [ContactController::class, 'relacionarUserContact']);
Route::post('prueba', [AuthController::class, 'CreateContact']);

Route::prefix('CoursesProgress')->group(function () {
    Route::get('/', [CourseProgressController::class, 'GetAll']);
    Route::post('/', [CourseProgressController::class, 'Create']);

});


Route::post('CreateLeadMSKCRM', [LeadController::class, 'CreateLeadMSKCRM']);
Route::get('Contacts', [ContactController::class, 'Contacts']);
Route::get('Contacts/{id}', [ContactController::class, 'ContactById']);
Route::post('Contact', [ContactController::class, 'Contact']);


Route::post('SwitchLike', [LikeController::class, 'SwitchLike'])->middleware('auth:api');

Route::prefix('om')->group(function () {
    Route::get('CreateAccessToken', [ZohoOMController::class, 'CreateAccessToken']);
    Route::get('GetLeads', [ZohoOMController::class, 'GetLeads']);
});

Route::prefix('crm')->group(function () {
    Route::get('CreateRefreshToken', [ZohoController::class, 'CreateRefreshToken']);
    Route::get('CreateAccessToken', [ZohoController::class, 'CreateAccessTokenDB']);

    Route::get('GetByEmail/{module}/{email}', [ZohoController::class, 'GetByEmail']);

    Route::get('GetLeads', [ZohoController::class, 'GetLeads']);
    Route::get('Leads/{id}', [ZohoController::class, 'GetByIdLeads']);
    Route::post('CreateLeads', [ZohoController::class, 'CreateLeads']);
    Route::put('UpdateLeads/{id}', [ZohoController::class, 'UpdateLeads']);
    Route::delete('DeleteLeads/{id}', [ZohoController::class, 'DeleteLeads']);

    Route::post('ConvertLead/{id}', [ZohoController::class, 'ConvertLead']);

    Route::get('Contacts', [ZohoController::class, 'GetContacts']);
    Route::post('CreateContacts', [ZohoController::class, 'CreateContacts']);
    Route::get('Contacts/{id}', [ZohoController::class, 'GetByIdContacts']);
    // Route::post('ConvertLeads', [ZohoController::class, 'ConvertLeads']);
    // Route::post('CreateLeads', [ZohoController::class, 'CreateLeads']);
    Route::delete('DeleteContacts/{id}', [ZohoController::class, 'DeleteContacts']);

    Route::get('Contracts', [ZohoController::class, 'GetContracts']);
    // Route::get('Products', [ZohoController::class, 'GetContracts']);
    // Route::get('Contracts', [ZohoController::class, 'GetContracts']);


    Route::post('CreateLeadHomeContactUs', [ZohoController::class, 'CreateLeadHomeContactUs']);

    Route::post('CreateLeadHomeNewsletter', [ZohoController::class, 'CreateLeadHomeNewsletter']);

    Route::get('GetLeadFieldsInCRM/{module}', [ZohoController::class, 'GetLeadFieldsInCRM']);
});

Route::get('store/professions', function () {
    $professions = [
        [
            'id' => 1,
            'name' => 'Personal médico'
        ],
        [
            'id' => 2,
            'name' => 'Personal de enfermería y auxiliares'
        ],
        [
            'id' => 3,
            'name' => 'Otra profesión'
        ],
    ];
    return response()->json($professions);
});

Route::get('newsletter/specialities', function () {

    $specialties = TopicInterest::all();

    return response()->json($specialties);
});

Route::get('professions', function () {
    $professions = Profession::all();
    return response()->json($professions);
});
Route::get('specialities-old', function () {
    $specialities = Speciality::all();
    return response()->json($specialities);
});
Route::get('specialities', function () {
//////Request solicitado
    // {
    //     "specialities_group": { // esto listaria las solapas de excel
    //         "profession_id" : [
    //             {"id":1,"name":"epecialidad_1"},
    //             {"id":2,"name":"epecialidad_2"}
    //         ],
    //     }
    // }

//////Opcion tres
    $professions = Profession::with('specialities')->get();
    $specialities_group = [];
    foreach( $professions as $p){
        $spData = [];
        foreach($p->specialities as $sp){
            array_push($spData, [ "id" => $sp->id, "name" => $sp->name ]);
        }
        $newgroup = [ $p->id => $spData ];
        array_push($specialities_group, $newgroup);
    }
    return response()->json([
        "specialities_group" => $specialities_group
    ]);
    
//////Opcion uno que esta genial
    // $professions = Profession::with('specialities')->get();
    // return response()->json($professions);
  
    // $professionSpecialities = ProfessionSpeciality::with('profession')->get();

//////Opcion dos
    // $professionSpecialities = ProfessionSpeciality::all();
    // $data = [];
    // foreach ($professionSpecialities as $ps) {
    //     $data[] = [
    //         'id' => $ps->id,
    //         'profession_name' => $ps->profession->name,
    //         'speciality_name' => $ps->speciality->name,
    //     ];
    // }
    // return response()->json($data);
    //respuesta
    // [
    //     {
    //         "id": 1,
    //         "profession_name": "Personal médico",
    //         "speciality_name": "Alergia e inmunología"
    //     },
    //     {
    //         "id": 2,
    //         "profession_name": "Personal médico",
    //         "speciality_name": "Anatomía patológica"
    //     },
    //     {
    //         "id": 3,
    //         "profession_name": "Personal médico",
    //         "speciality_name": "Anestesiología"
    //     },
    //     {
    //         "id": 4,
    //         "profession_name": "Personal médico",
    //         "speciality_name": "Auditoría y administración sanitaria"
    //     },
    // ]

//////prueba
    // $ids = [ 
    //     "asdasd",
    //     "dasdasdasdasdasd"
    // ];
    // $specialities_group = [ 
    //         $ids[0] => [
    //             [ "id" => 1, "name" => "epecialidad_1" ],
    //             [ "id" => 2, "name" => "epecialidad_2" ]

    //         ],
    //         $ids[1] => [
    //             [ "id" => 1, "name" => "epecialidad_3" ],
    //             [ "id" => 2, "name" => "epecialidad_4" ]

    //         ],
    //     ];

    
    // return response()->json([
    //     "specialities_group" => $specialities_group
    // ]);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'GetProducts']);
    Route::post('/{id}', [ProductController::class, 'CreateLeads']);
});

Route::prefix('webhook/rebill')->group(function () {
    Route::any('/newPayment', [RebillController::class, 'newPayment']);
    Route::any('/changeStatusPayment', [RebillController::class, 'changeStatusPayment']);
    Route::any('/newSubscription', [RebillController::class, 'newSubscription']);
    Route::any('/changeStatusSubscription', [RebillController::class, 'changeStatusSubscription']);
    Route::any('/test', [RebillController::class, 'test']);
    // Route::post("/payloadZohoCRMMSK", [RebillController::class, "payloadZohoCRMMSK"]);

});

Route::get("omApiPayments", function () {

    $apiPayments = DB::connection('omApiPayments')->select('SELECT * FROM payment_links');

    return response()->json($apiPayments);
});

Route::post("sso/link", [SSOController::class, "getLMSLink"]);
Route::post("/getCountryByIP", [CountryController::class, "getCountryByIP"]);
Route::get("/crm/products", [ZohoController::class, 'getProductsCRM']);

