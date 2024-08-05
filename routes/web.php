<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\AjaxController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('route:clear');
    return '<h1>Cache facade value cleared</h1>';
});
Route::get('/', function () {
    return view("manufacturer.onboarding");
});
Route::get('/payment', function () {
    return view("payment");
});
Route::get('/payment/success', function () {
    return 'success';
});
Route::get('/payment/failure', function () {
    return 'failure';
});
Route::get('manufacturer/login', [ManufacturerController::class, 'loginManufacturer'])->name('manufacturer.login');
Route::post('manufacturer/login', [ManufacturerController::class, 'authenticate']);
Route::middleware(['auth:manufacturer'])->group(function () {
    Route::get('manufacturer/dashboard', [ManufacturerController::class, 'dashboard'])->name('manufacturer.dashboard');
    Route::get('manufacturer/profile', [ManufacturerController::class, 'ManufacturerProfile'])->name('profile');
    Route::match(['get', 'post'], 'manufacturer/enquiry', [ManufacturerController::class, 'enquiries'])->name('manufacturer.enquiry');
    Route::match(['get', 'post'], 'manufacturer/manage-locations', [ManufacturerController::class, 'manageLocations'])->name('manufacturer.manage-locations');
    Route::get('manufacturer/settings', [ManufacturerController::class, 'settings'])->name('manufacturer.settings');
    Route::post('/manufacturer/settings', [ManufacturerController::class, 'updateSettings'])->name('manufacturer.updateSettings');
    Route::get('/add-plant', [ManufacturerController::class, 'AddPlant'])->name('AddPlant');
    Route::get('/view-plant/{id}', [ManufacturerController::class, 'ViewPlant'])->name('ViewPlant');
    Route::get('/edit-plant/{id}', [ManufacturerController::class, 'EditPlant'])->name('EditPlant');
    Route::post('/manufacturer/update-password/{id}', [ManufacturerController::class, 'updateManufacturerPassword'])->name('manufacturer.updatePassword');

    Route::post('/manufacturers', [ManufacturerController::class, 'saveManufacturer']);
    Route::put('/manufacturers/{id}', [ManufacturerController::class, 'updateManufacturer'])->name('manufacturers.update');
    Route::post('/plant', [ManufacturerController::class, 'savePlant'])->name('savePlant');
    Route::post('/update-plant/{id}', [ManufacturerController::class, 'updatePlant'])->name('updatePlant');
    Route::delete('/delete-sales-manager/{id}', [AjaxController::class, 'deleteSalesManager'])->name('deleteSalesManager');
    Route::delete('/delete-plant', [ManufacturerController::class, 'delete'])->name('delete-plant');

    // Route::post('specifications', [AjaxController::class, 'saveSpecifications']);
    //Route::post('specifications/{id}', [AjaxController::class, 'updateSpecifications'])->name('updateSpecification');
    Route::post('/logout', [ManufacturerController::class, 'logout'])->name('manufacturer.logout');
    Route::post('/save-specification', [AjaxController::class, 'saveSpecifications'])->name('save-specification');
    Route::post('/remove-specification/{id}', [AjaxController::class, 'removeSpecification'])->name('remove-specification');
    Route::post("/toggleRequestStatus", [AjaxController::class, 'toggleRequestStatus'])->name('toggleRequestStatus');
    Route::post("/delete-photo/{id}", [ManufacturerController::class, 'deletePhoto'])->name('delete-photo');

    Route::post("/delete-spec-photo/{id}", [ManufacturerController::class, 'deleteSpecPhoto'])->name('delete-spec-photo');

    Route::post("/delete-profile-photo/{id}", [ManufacturerController::class, 'deleteProfilePhoto'])->name('delete-profile-photo');
    Route::post('update-specifications/{id}', [AjaxController::class, 'updateSpecifications'])->name('updateSpecification');
});
Route::group(['prefix' => 'manufacturer', 'as' => 'manufacturer.'], function () {

    Route::get("/forget-password", [ManufacturerController::class, 'forget'])->name('forget.password');
    Route::get("/reset-password", [ManufacturerController::class, 'reset'])->name('reset.password');

    Route::post('reset-password', [ManufacturerController::class, 'submitResetPasswordForm'])->name('reset.password.post');
    Route::post('forget-password', [ManufacturerController::class, 'submitResetPasswordFormEmail'])->name('forget.password.post');
});


Route::get('/signup', [ManufacturerController::class, 'ManufacturerSignup'])->name('signup');
Route::post('/signup', [ManufacturerController::class, 'saveManufacturer'])->name('saveManufacturer');
Route::post('/check-email', [ManufacturerController::class, 'checkEmail'])->name('checkEmail');

// Admin routes 
Route::post('set_status', [AjaxController::class, 'set_status'])->name('set_status');
Route::post('set_statuss', [AjaxController::class, 'set_statuss'])->name('set_statuss');
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('login', [AdminController::class, 'login'])->name('login');
    Route::post('login', [AdminController::class, 'authenticate'])->name('authenticate');

    Route::get("/forget-password", [AdminController::class, 'forget'])->name('forget.password');
    Route::get("/reset-password", [AdminController::class, 'reset'])->name('reset.password');

    Route::post('reset-password', [AdminController::class, 'submitResetPasswordForm'])->name('reset.password.post');
    Route::post('forget-password', [AdminController::class, 'submitResetPasswordFormEmail'])->name('forget.password.post');



    Route::middleware(['auth:admin'])->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('profile', [AdminController::class, 'profile'])->name('profile');
        Route::post('profile', [AdminController::class, 'updateProfile'])->name('profile.update');
        Route::post('change-password', [AdminController::class, 'changePassword'])->name('change.password');
        Route::post('update-password/{id}', [AdminController::class, 'updateManufacturerPassword'])->name('updatePassword');


        // enquries
        Route::get('enquiries', [AdminController::class, 'enquiries'])->name('enquiries');

        // community owners
        Route::get('community-owners', [AdminController::class, 'communityOwners'])->name('community.owners');
        Route::get('community-owners/{slug}', [AdminController::class, 'communityOwnersShow'])->name('community.owners.show');
        Route::get('community/{slug}', [AdminController::class, 'communityShow'])->name('community.show');

        // manufracturers
        Route::get('plants', [AdminController::class, 'manufracturers'])->name('manufracturers');
        Route::get('plants/requests', [AdminController::class, 'manufracturers_requests'])->name('manufracturers.requests');
        Route::post('plants/requests/approve/{id}', [AdminController::class, 'manufracturers_requests_approve'])->name('manufracturers.requests.approve');
        Route::get('plants/{slug}', [AdminController::class, 'manufracturersShow'])->name('manufracturers.show');


        //   settings
        Route::get('settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('save_setting', [AdminController::class, 'save_setting'])->name('save_setting');


        //Route::post('set_status', [AdminController::class, 'set_status'])->name('set_status');
        Route::post('remove-profile', [AdminController::class, 'removeProfile'])->name('remove.profile');
        Route::post('activate-plant', [AjaxController::class, 'activatePlant'])->name('activate.plant');
        Route::post('inactivate-plant', [AjaxController::class, 'inactivatePlant'])->name('inactivate.plant');
    });
});