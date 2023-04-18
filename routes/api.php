<?php

// use Illuminate\Http\Request;
use App\Http\Controllers\ZohoOMController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ZohoController;
use App\Http\Controllers\Zoho2Controller;

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
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('user', [AuthController::class, 'user'])->middleware('auth:api');

Route::get('prueba', [Zoho2Controller::class, 'prueba']);

Route::prefix('om')->group(function () {
    Route::get('CreateAccessToken', [ZohoOMController::class, 'CreateAccessToken']);
    Route::get('GetLeads', [ZohoOMController::class, 'GetLeads']);

});

Route::get('CreateAccessToken', [ZohoController::class, 'CreateAccessToken']);
Route::get('GetLeads', [ZohoController::class, 'GetLeads']);
Route::get('Leads/{id}', [ZohoController::class, 'GetByIdLeads']);
Route::put('UpdateLeads/{id}', [ZohoController::class, 'UpdateLeads']);
Route::post('CreateLeads', [ZohoController::class, 'CreateLeads']);
Route::delete('DeleteLeads/{id}', [ZohoController::class, 'DeleteLeads']);

Route::post('ConvertLead/{id}', [ZohoController::class, 'ConvertLead']);

Route::get('Contacts', [ZohoController::class, 'GetContacts']);
Route::get('Contacts/{id}', [ZohoController::class, 'GetByIdContacts']);
// Route::post('ConvertLeads', [ZohoController::class, 'ConvertLeads']);
// Route::post('CreateLeads', [ZohoController::class, 'CreateLeads']);

Route::get('Contracts', [ZohoController::class, 'GetContracts']);