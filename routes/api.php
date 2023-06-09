<?php

// use Illuminate\Http\Request;
use App\Http\Controllers\Webhooks\RebillController;
use App\Http\Controllers\ZohoOMController;
use App\Http\Controllers\ZohoController;
use App\Http\Controllers\ZohoWorkflowController;
use App\Models\Profession;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LikeController;
use App\Models\Like;
use GuzzleHttp\Psr7\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('signup', [AuthController::class, 'signup']);
Route::post('signupForCRM', [AuthController::class, 'signupForCRM']);
Route::post('salesForCRM', [ZohoWorkflowController::class, 'salesForCRM']);
Route::post('setNewPasswordFromMSK', [ZohoWorkflowController::class, 'setNewPasswordFromMSK']);
Route::post('/ValidatedUser', [ZohoWorkflowController::class, 'ValidatedUser']);

Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('user', [AuthController::class, 'user'])->middleware('auth:api');
Route::get('/profile/{email}', [AuthController::class, 'GetProfile'])->middleware("auth:api");
Route::put('/profile/{email}', [AuthController::class, 'PutProfile']);
Route::post('/ValidatePasswordChange', [AuthController::class, 'ValidatePasswordChange']);
Route::post('/RequestPasswordChange', [AuthController::class, 'RequestPasswordChange']);
Route::post('/newPassword', [AuthController::class, 'newPassword']);

Route::get('prueba', [ContactController::class, 'relacionarUserContact']);
Route::post('prueba', [AuthController::class, 'CreateContact']);

Route::post('CreateLeadMSKCRM', [LeadController::class, 'CreateLeadMSKCRM']);
Route::get('Contacts', [ContactController::class, 'Contacts']);
Route::get('Contacts/{id}', [ContactController::class, 'ContactById']);



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

    $specialties = [
        [
            'id' => 1,
            'name' => 'Cardiología'
        ],
        [
            'id' => 2,
            'name' => 'Ginecología'
        ],
        [
            'id' => 3,
            'name' => 'Diabetes'
        ],
        [
            'id' => 4,
            'name' => 'Psicología'
        ],
        [
            'id' => 5,
            'name' => 'Cirugía'
        ],
        [
            'id' => 6,
            'name' => 'Medicina general'
        ],
        [
            'id' => 7,
            'name' => 'Nutrición'
        ],
        [
            'id' => 8,
            'name' => 'Infectología'
        ],
        [
            'id' => 9,
            'name' => 'Obstetricia'
        ],
        [
            'id' => 10,
            'name' => 'Hematología'
        ],
        [
            'id' => 11,
            'name' => 'Emergentología'
        ],
        [
            'id' => 12,
            'name' => 'Oncología'
        ],
        [
            'id' => 13,
            'name' => 'Gastroenterología'
        ],
        [
            'id' => 14,
            'name' => 'Medicina intensiva'
        ],
        [
            'id' => 15,
            'name' => 'Anestesiología y dolor'
        ],
        [
            'id' => 16,
            'name' => 'Pediatría'
        ],
        [
            'id' => 17,
            'name' => 'Dermatología'
        ],
        [
            'id' => 18,
            'name' => 'Geriatría'
        ],
        [
            'id' => 19,
            'name' => 'Psiquiatría'
        ],
        [
            'id' => 20,
            'name' => 'Diagnóstico por imágenes'
        ],
        [
            'id' => 21,
            'name' => 'Endocrinología'
        ],
        [
            'id' => 22,
            'name' => 'Medicina interna'
        ],
        [
            'id' => 23,
            'name' => 'Neurología'
        ],
        [
            'id' => 24,
            'name' => 'Oftalmología'
        ],
        [
            'id' => 25,
            'name' => 'Traumatología'
        ],
        [
            'id' => 26,
            'name' => 'Otorrinolaringología'
        ]
    ];

    return response()->json($specialties);
});

Route::get('professions', function () {
    $professions = Profession::all();
    return response()->json($professions);
});
Route::get('specialities', function () {
    $specialities = Speciality::all();
    return response()->json($specialities);
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
});

Route::get("omApiPayments", function () {

    $apiPayments = DB::connection('omApiPayments')->select('SELECT * FROM payment_links');

    return response()->json($apiPayments);
});