<?php

// use Illuminate\Http\Request;
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

Route::middleware("auth:api")->get('/profile/{email}', function (Request $request, $email) {
    $user = User::where("email", $email)->first();
    return response()->json($user);
});

Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('user', [AuthController::class, 'user'])->middleware('auth:api');

Route::get('prueba', [ContactController::class, 'relacionarUserContact']);
Route::post('prueba', [AuthController::class, 'CreateContact']);

Route::post('CreateLeadMSKCRM', [LeadController::class, 'CreateLeadMSKCRM']);
Route::get('Contacts', [ContactController::class, 'Contacts']);

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

Route::get('professions', function () {
    $professions = Profession::all();
    return response()->json($professions);
});
Route::get('specialities', function () {
    $specialities = Speciality::all();
    return response()->json($specialities);
});