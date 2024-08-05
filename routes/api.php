<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\ManufacturerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/change-password', [UserController::class, 'change_password']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'userDetails']);

    Route::post('update-profile', [UserController::class, 'updateProfile']);
    Route::get('/get-notifications', [UserController::class, 'getNotification']);
    Route::post('/clear-notifications', [UserController::class, 'ClearNotification']);
});
Route::post('/community-details', [CommunityController::class, 'getCommunityDetails']);
Route::post('/delete-community-image', [CommunityController::class, 'deleteCommunityPhoto']);

Route::post('/delete-managers', [CommunityController::class, 'deletePropertyManagers']);
Route::post('/delete-manager-image', [CommunityController::class, 'deleteSalesManagerPhoto']);
Route::post('/contact-manufacturers', [UserController::class, 'contactManufacturer']);
/* -------------------------------community----------------------------- */
Route::post('/add-community', [CommunityController::class, 'addCommunity']);
Route::post('/communities-listing', [CommunityController::class, 'communityListing']);
Route::post('/update-community', [CommunityController::class, 'updateCommunity']);
Route::get('/get-locations', [UserController::class, 'getLocations']);
Route::get('/location-details', [UserController::class, 'LocationDetails']);
Route::post('/manufacturers', [ManufacturerController::class, 'saveManufacturer']);
Route::post('/plant', [ManufacturerController::class, 'savePlant']);
Route::get('/plants', [UserController::class, 'plantListing']);
Route::post('/plant-details', [UserController::class, 'plantDetails']);
Route::post('/manufacturers', [UserController::class, 'manufacturerListing']);
Route::post('/manufacturers-details', [UserController::class, 'getManufacturerDetails']);
Route::get('/filter_static_data', [UserController::class, 'filter_static_data']);
