<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InvestorBookingController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the 'api' middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => 'api'], function () {
    
    Route::post('startup-register', [App\Http\Controllers\Api\UserController::class, 'startup_register']);
    Route::post('register', [App\Http\Controllers\Api\UserController::class, 'userRegister']);
    Route::post('personal-information',[App\Http\Controllers\Api\StartupController::class,'personal_information']);
    Route::post('investor-register', [App\Http\Controllers\Api\UserController::class, 'investor_register']);
    Route::post('send-otp/{id}', [App\Http\Controllers\Api\UserController::class, 'send_otp']);
    Route::post('confirm-otp', [App\Http\Controllers\Api\UserController::class, 'confirm_otp']);
    Route::post('save-contact', [App\Http\Controllers\Api\UserController::class, 'save_contact']);
    Route::post('user-login', [App\Http\Controllers\Api\UserController::class, 'user_login']);
    Route::get('countries',[App\Http\Controllers\Api\CountryController::class,'all_countries']);
    Route::post('investor-profile',[App\Http\Controllers\Api\InvestorController::class,'investor_profile']);
    Route::post('reset-password',[App\Http\Controllers\Api\UserController::class,'reset_password']);
    Route::post('/check-user-email-verfication', [App\Http\Controllers\Api\UserController::class, 'check_user_email_verfication']);
    Route::post('/forget-password',[App\Http\Controllers\Api\UserController::class,'forget_password']);
    Route::post('/check-user-reset-password-verfication',[App\Http\Controllers\Api\UserController::class,'check_user_reset_password_verfication']);
    Route::post('/updated-reset-password',[App\Http\Controllers\Api\UserController::class,'updated_reset_password']);
    Route::post('send-notifications',[App\Http\Controllers\Api\NotificationController::class,'sendNotification']);
    Route::get('notifications-count/{id}',[App\Http\Controllers\Api\NotificationController::class,'getTotalCountOfNotifications']);
    Route::post('send-mail-notifications',[App\Http\Controllers\Api\NotificationController::class,'sendMailNotification']);
    Route::post('notifications-delete/{id}',[App\Http\Controllers\Api\NotificationController::class,'destroy_notifications']);
});

Route::group(['middleware' => ['api','jwt.verify']], function () {
   
    Route::post('business-information',[App\Http\Controllers\Api\StartupController::class,'business_information']);
    Route::post('update-startup-personal-info/{id}',[App\Http\Controllers\Api\StartupController::class,'update_personal_information']);
    Route::post('business-information-update/{userid}',[App\Http\Controllers\Api\StartupController::class,'business_information_update']);
    Route::get('single-startup/{id}', [App\Http\Controllers\Api\StartupController::class, 'get_single_startup']);
  
    Route::get('get-business-information/{id}',[App\Http\Controllers\Api\StartupController::class,'get_business_information']);
   
   
    Route::get('get-basic-information/{id}',[App\Http\Controllers\Api\DocumentsController::class,'get_basic_information']);
    Route::post('basic-information',[App\Http\Controllers\Api\DocumentsController::class,'basic_information']);
    Route::post('bank-details',[App\Http\Controllers\Api\BankDetailsController::class,'bank_details']);
    Route::get('get-bank-information/{id}',[App\Http\Controllers\Api\BankDetailsController::class,'get_bank_information']);
    Route::post('investor-type-information',[App\Http\Controllers\Api\InvestorController::class,'investor_type_information']);
    Route::get('get-investor-type-information/{id}', [App\Http\Controllers\Api\InvestorController::class, 'get_investor_type_information']);
    Route::post('update-profile', [App\Http\Controllers\Api\UserController::class, 'update_profile']);
    Route::get('single-user/{id}', [App\Http\Controllers\Api\UserController::class, 'get_single_user']);
    
    Route::get('single-front-data/{id}', [App\Http\Controllers\Api\UserController::class, 'get_front_user']);
    Route::get('get-user-count', [App\Http\Controllers\Api\UserController::class, 'get_user_count']);
    Route::post('join_to_invest', [App\Http\Controllers\Api\UserController::class, 'join_to_invest']);
    Route::post('angel-investor-terms', [App\Http\Controllers\Api\InvestorController::class, 'angel_investor_terms']);
    Route::get('get-angel-investor-terms/{id}', [App\Http\Controllers\Api\InvestorController::class, 'get_angel_investor_terms']);
    Route::post('accredited-investor-terms', [App\Http\Controllers\Api\InvestorController::class, 'accredited_investor_terms']);
    Route::get('get-accredited-investor-terms/{id}', [App\Http\Controllers\Api\InvestorController::class, 'get_accredited_investor_terms']);

    Route::get('get-all-investors',[App\Http\Controllers\Api\InvestorController::class,'get_all_investors']);
    Route::get('get-investor-count', [App\Http\Controllers\Api\InvestorController::class, 'get_investor_count']);
   
    Route::get('get-all-startup',[App\Http\Controllers\Api\StartupController::class,'get_all_startup']);
    Route::get('get-startup-count',[App\Http\Controllers\Api\StartupController::class,'get_startup_count']);
    Route::post('update-startup-status/{id}',[App\Http\Controllers\Api\StartupController::class,'updateApprovalStatus']);
    Route::post('update-startup-stage/{id}',[App\Http\Controllers\Api\StartupController::class,'updateApprovalStage']);
    Route::post('update-user-role/{id}',[App\Http\Controllers\Api\UserController::class,'updateUserRole']);
    Route::post('update-user-country/{id}',[App\Http\Controllers\Api\UserController::class,'updateUserCountry']);
    Route::get('/download/{filename}', [App\Http\Controllers\Api\UserController::class, 'handleDownload'])->name('download');
    Route::post('update-user/{id}', [App\Http\Controllers\Api\UserController::class, 'updateUser']);

    Route::post('update-investor-status/{id}',[App\Http\Controllers\Api\InvestorController::class,'updateApprovalStatus']);

    Route::post('update-investor-personal-info/{id}',[App\Http\Controllers\Api\InvestorController::class,'updateInvestorInformation']);
    
    Route::post('store-bank-details', [App\Http\Controllers\Api\UserController::class, 'store_bank_detail']);
    Route::post('update-bank-details/{id}', [App\Http\Controllers\Api\UserController::class, 'update_bank_detail']);

    Route::post('document-upload/{id}', [App\Http\Controllers\Api\UserController::class, 'document_upload']);

    Route::post('update-business-details/{id}', [App\Http\Controllers\Api\UserController::class, 'business_detail_update']);

     // Countries route 
     Route::get('country/{id}',[App\Http\Controllers\Api\CountryController::class,'single_country']);
     Route::get('get-all-business-details',[App\Http\Controllers\Api\StartupController::class,'get_all_business_details']);
      Route::get('get-single-business-details/{id}',[App\Http\Controllers\Api\StartupController::class,'get_single_business_details']);
     Route::get("get-buisness-id/{id}",[App\Http\Controllers\Api\StartupController::class,'get_buisness_id']);
     Route::get("fund-raise-count",[App\Http\Controllers\Api\StartupController::class,'get_fund_raise_count']);
   
     Route::delete('startups/{id}', [App\Http\Controllers\Api\StartupController::class, 'destroy']);
     Route::post('booking',[App\Http\Controllers\Api\InvestorBookingController::class,'booking']);

     Route::post('fund-raise-store',[App\Http\Controllers\Api\StartupController::class,'fund_raise_information_store']);
     Route::get('get-all-funds/{id}',[App\Http\Controllers\Api\StartupController::class,'get_all_funds']);
     Route::post('update-fund-status/{id}',[App\Http\Controllers\Api\StartupController::class,'updateFundStatus']);
     Route::post('update-status/{id}',[App\Http\Controllers\Api\StartupController::class,'updateStatus']);
     Route::post('update-investor-status/{id}',[App\Http\Controllers\Api\InvestorController::class,'update_investor_status']);
     Route::post('update-investor-approvalstatus/{id}',[App\Http\Controllers\Api\InvestorController::class,'updateApprovalStatus']);
     Route::post('update-user-status/{id}',[App\Http\Controllers\Api\UserController::class,'updateUserStatus']);

     Route::get('get-all-users',[App\Http\Controllers\Api\AdminController::class,'get_all_users']);
     Route::post('terms-and-conditions',[App\Http\Controllers\Api\AdminController::class,'terms_and_conditions']);
     Route::post('privacy-policies',[App\Http\Controllers\Api\AdminController::class,'privacy_policies']);
     Route::get('get-terms-and-conditions',[App\Http\Controllers\Api\AdminController::class,'get_terms_and_conditions']);
     Route::get('get-privacy-policies',[App\Http\Controllers\Api\AdminController::class,'get_privacy_policies']);
     
    
    
     Route::post('user-delete/{id}',[App\Http\Controllers\Api\AdminController::class,'destroy_user_data']);
     Route::delete('fund-delete/{id}',[App\Http\Controllers\Api\AdminController::class,'destroy_fund']);
     Route::get('fund-single/{id}',[App\Http\Controllers\Api\AdminController::class,'get_single_fund']);

     Route::get('get-all-active-funds',[\App\Http\Controllers\Api\AdminController::class,'get_all_active_funds']);
     Route::get('totalcount-active-funds',[\App\Http\Controllers\Api\AdminController::class,'total_count_active_funds']);
    
     Route::delete('investor-delete/{id}',[App\Http\Controllers\Api\AdminController::class,'destroy_investor_data']);
     Route::get('single-investor/{id}', [App\Http\Controllers\Api\AdminController::class, 'get_single_investor']);
     Route::get('get-admin-data', [App\Http\Controllers\Api\AdminController::class, 'get_admin_data']);
     Route::post('update-admin', [App\Http\Controllers\Api\AdminController::class, 'update_admin_data']);

     Route::get('check-user-approval-status',[App\Http\Controllers\Api\UserController::class,'check_user_approval_status']);
 
     Route::get('get-all-notifications', [App\Http\Controllers\Api\NotificationController::class,'getAllNotifications']);
     Route::get('get-notifications/{id}', [App\Http\Controllers\Api\NotificationController::class, 'getNotifications']);
     
     Route::get('unread-notifications-count/{id}',[App\Http\Controllers\Api\NotificationController::class,'getCountOfUnreadNotifications']);
     Route::post('update-notifications/{id}',[App\Http\Controllers\Api\NotificationController::class,'updateNotification']);

    Route::post('notification-config-store',[App\Http\Controllers\Api\OptionController::class,'notificationConfigStore']);
    Route::get('get-options/{id}',[App\Http\Controllers\Api\OptionController::class,'getOptions']);
   
   
    });