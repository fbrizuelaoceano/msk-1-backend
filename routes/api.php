<?php

// use Illuminate\Http\Request;
use App\Http\Controllers\ZohoOMController;
use App\Http\Controllers\ZohoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;


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

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('user', [AuthController::class, 'user'])->middleware('auth:api');

Route::get('prueba', [AuthController::class, 'CreateContact']);
Route::post('prueba', [AuthController::class, 'CreateContact']);


//Route::post('CreateLeadMSKCRM', [LeadController::class, 'CreateLeadMSKCRM']);
Route::post('CreateLeadMSKCRM', [ZohoController::class, 'CreateLeadFunction']);

Route::prefix('om')->group(function () {
    Route::get('CreateAccessToken', [ZohoOMController::class, 'CreateAccessToken']);
    Route::get('GetLeads', [ZohoOMController::class, 'GetLeads']);

});

Route::prefix('crm')->group(function () {
    Route::get('CreateRefreshToken', [ZohoController::class, 'CreateRefreshToken']);
    Route::get('CreateAccessToken', [ZohoController::class, 'CreateAccessToken']);

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


    Route::get('GetLeadFieldsInCRM/{module}', [ZohoController::class, 'GetLeadFieldsInCRM']);
});