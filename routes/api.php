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
    Route::post('personal-information', [App\Http\Controllers\Api\StartupController::class, 'personal_information']);
    Route::post('investor-register', [App\Http\Controllers\Api\UserController::class, 'investor_register']);
    Route::post('send-otp/{id}', [App\Http\Controllers\Api\UserController::class, 'send_otp']);
    Route::post('confirm-otp', [App\Http\Controllers\Api\UserController::class, 'confirm_otp']);
    Route::post('save-contact', [App\Http\Controllers\Api\UserController::class, 'save_contact']);
    Route::post('user-login', [App\Http\Controllers\Api\UserController::class, 'user_login']);
    Route::get('countries', [App\Http\Controllers\Api\CountryController::class, 'all_countries']);
    Route::post('investor-profile', [App\Http\Controllers\Api\InvestorController::class, 'investor_profile']);
    Route::post('reset-password', [App\Http\Controllers\Api\UserController::class, 'reset_password']);
    Route::post('/check-user-email-verfication', [App\Http\Controllers\Api\UserController::class, 'check_user_email_verfication']);
    Route::post('/forget-password', [App\Http\Controllers\Api\UserController::class, 'forget_password']);
    Route::post('/check-user-reset-password-verfication', [App\Http\Controllers\Api\UserController::class, 'check_user_reset_password_verfication']);
    Route::post('/updated-reset-password', [App\Http\Controllers\Api\UserController::class, 'updated_reset_password']);
    Route::post('send-notifications', [App\Http\Controllers\Api\NotificationController::class, 'sendNotification']);
    Route::get('notifications-count/{id}', [App\Http\Controllers\Api\NotificationController::class, 'getTotalCountOfNotifications']);
    Route::post('send-mail-notifications', [App\Http\Controllers\Api\NotificationController::class, 'sendMailNotification']);
    Route::post('notifications-delete/{id}', [App\Http\Controllers\Api\NotificationController::class, 'destroy_notifications']);
    Route::post('confirm-email-otp', [App\Http\Controllers\Api\UserController::class, 'confirmEmailOtp']);
    Route::post('resend-otp', [App\Http\Controllers\Api\UserController::class, 'ResendOtp']);
    Route::post('phone-verify', [App\Http\Controllers\Api\StartupController::class, 'PhoneVerify']);
});

Route::group(['middleware' => ['api']], function () {

    Route::post('business-information', [App\Http\Controllers\Api\StartupController::class, 'business_information']);
    Route::post('update-startup-personal-info/{id}', [App\Http\Controllers\Api\StartupController::class, 'update_personal_information']);
    Route::post('business-information-update/{userid}', [App\Http\Controllers\Api\StartupController::class, 'business_information_update']);
    Route::get('single-startup/{id}', [App\Http\Controllers\Api\StartupController::class, 'get_single_startup']);

    Route::get('get-business-information/{id}', [App\Http\Controllers\Api\StartupController::class, 'get_business_information']);
    Route::get('get-business-information-bid/{id}', [App\Http\Controllers\Api\StartupController::class, 'get_business_information_business_id']);

    Route::get('get-basic-information/{id}', [App\Http\Controllers\Api\DocumentsController::class, 'get_basic_information']);
    Route::post('basic-information', [App\Http\Controllers\Api\DocumentsController::class, 'basic_information']);
    Route::post('bank-details', [App\Http\Controllers\Api\BankDetailsController::class, 'bank_details']);
    Route::get('get-bank-information/{id}', [App\Http\Controllers\Api\BankDetailsController::class, 'get_bank_information']);
    Route::post('investor-type-information', [App\Http\Controllers\Api\InvestorController::class, 'investor_type_information']);
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

    Route::get('get-all-investors', [App\Http\Controllers\Api\InvestorController::class, 'get_all_investors']);
    Route::get('get-investor-count', [App\Http\Controllers\Api\InvestorController::class, 'get_investor_count']);

    Route::get('get-all-startup', [App\Http\Controllers\Api\StartupController::class, 'get_all_startup']);
    Route::get('get-startup-count', [App\Http\Controllers\Api\StartupController::class, 'get_startup_count']);
    Route::post('update-startup-status/{id}', [App\Http\Controllers\Api\StartupController::class, 'updateApprovalStatus']);
    Route::post('update-startup-stage/{id}', [App\Http\Controllers\Api\StartupController::class, 'updateApprovalStage']);
    Route::post('update-user-role/{id}', [App\Http\Controllers\Api\UserController::class, 'updateUserRole']);
    Route::post('update-user-country/{id}', [App\Http\Controllers\Api\UserController::class, 'updateUserCountry']);
    Route::get('/download/{filename}', [App\Http\Controllers\Api\UserController::class, 'handleDownload'])->name('download');
    Route::post('update-user/{id}', [App\Http\Controllers\Api\UserController::class, 'updateUser']);

    Route::post('update-investor-status/{id}', [App\Http\Controllers\Api\InvestorController::class, 'updateApprovalStatus']);

    Route::post('update-investor-personal-info/{id}', [App\Http\Controllers\Api\InvestorController::class, 'updateInvestorInformation']);

    Route::post('store-bank-details', [App\Http\Controllers\Api\UserController::class, 'store_bank_detail']);
    Route::post('update-bank-details/{id}', [App\Http\Controllers\Api\UserController::class, 'update_bank_detail']);

    Route::post('document-upload/{id}', [App\Http\Controllers\Api\UserController::class, 'document_upload']);

    Route::post('update-business-details/{id}', [App\Http\Controllers\Api\UserController::class, 'business_detail_update']);

    // Countries route 
    Route::get('country/{id}', [App\Http\Controllers\Api\CountryController::class, 'single_country']);
    Route::get('get-all-business-details', [App\Http\Controllers\Api\StartupController::class, 'get_all_business_details']);
    Route::get('get-all-invested-fund-details', [App\Http\Controllers\Api\StartupController::class, 'get_all_invested_fund_details']);
    Route::get('get-single-business-details/{id}', [App\Http\Controllers\Api\StartupController::class, 'get_single_business_details']);
    Route::get('get-single-closed-business-details/{id}', [App\Http\Controllers\Api\StartupController::class, 'get_single_closed_business_details']);
    Route::get("get-buisness-id/{id}", [App\Http\Controllers\Api\StartupController::class, 'get_buisness_id']);
    Route::get("fund-raise-count", [App\Http\Controllers\Api\StartupController::class, 'get_fund_raise_count']);
    Route::get("total-subscriber-count", [App\Http\Controllers\Api\InvestorController::class, 'get_total_subscriber_count']);
    Route::get("get-single-buisness-unit/{id}", [App\Http\Controllers\Api\StartupController::class, 'getSingleBusinessUnitInfo']);

    Route::delete('startups/{id}', [App\Http\Controllers\Api\StartupController::class, 'destroy']);
    Route::post('booking', [App\Http\Controllers\Api\InvestorBookingController::class, 'booking']);
    Route::get('get-booking-details/{id}', [App\Http\Controllers\Api\InvestorBookingController::class, 'getBookingDetails']);

    Route::post('fund-raise-store', [App\Http\Controllers\Api\StartupController::class, 'fund_raise_information_store']);
    Route::get('get-all-funds/{id}', [App\Http\Controllers\Api\StartupController::class, 'get_all_funds']);
    Route::post('update-fund-status/{id}', [App\Http\Controllers\Api\StartupController::class, 'updateFundStatus']);
    Route::post('update-status/{id}', [App\Http\Controllers\Api\StartupController::class, 'updateStatus']);
    Route::post('update-investor-status/{id}', [App\Http\Controllers\Api\InvestorController::class, 'update_investor_status']);
    Route::post('update-investor-approvalstatus/{id}', [App\Http\Controllers\Api\InvestorController::class, 'updateApprovalStatus']);
    Route::post('update-user-status/{id}', [App\Http\Controllers\Api\UserController::class, 'updateUserStatus']);

    Route::get('get-all-users', [App\Http\Controllers\Api\AdminController::class, 'get_all_users']);
    Route::post('terms-and-conditions', [App\Http\Controllers\Api\AdminController::class, 'terms_and_conditions']);
    Route::post('privacy-policies', [App\Http\Controllers\Api\AdminController::class, 'privacy_policies']);
    Route::get('get-terms-and-conditions', [App\Http\Controllers\Api\AdminController::class, 'get_terms_and_conditions']);
    Route::get('get-privacy-policies', [App\Http\Controllers\Api\AdminController::class, 'get_privacy_policies']);



    Route::post('user-delete/{id}', [App\Http\Controllers\Api\AdminController::class, 'destroy_user_data']);
    Route::delete('fund-delete/{id}', [App\Http\Controllers\Api\AdminController::class, 'destroy_fund']);
    Route::get('fund-single/{id}', [App\Http\Controllers\Api\AdminController::class, 'get_single_fund']);

    Route::get('get-all-active-funds', [\App\Http\Controllers\Api\AdminController::class, 'get_all_active_funds']);
    Route::get('totalcount-active-funds', [\App\Http\Controllers\Api\AdminController::class, 'total_count_active_funds']);

    Route::delete('investor-delete/{id}', [App\Http\Controllers\Api\AdminController::class, 'destroy_investor_data']);
    Route::get('single-investor/{id}', [App\Http\Controllers\Api\AdminController::class, 'get_single_investor']);
    Route::get('get-admin-data', [App\Http\Controllers\Api\AdminController::class, 'get_admin_data']);
    Route::post('update-admin', [App\Http\Controllers\Api\AdminController::class, 'update_admin_data']);
    Route::post('terms-and-conditions-by-admin', [App\Http\Controllers\Api\AdminController::class, 'terms_and_conditions']);

    Route::get('check-user-approval-status', [App\Http\Controllers\Api\UserController::class, 'check_user_approval_status']);

    Route::get('get-all-notifications', [App\Http\Controllers\Api\NotificationController::class, 'getAllNotifications']);
    Route::get('get-notifications/{id}', [App\Http\Controllers\Api\NotificationController::class, 'getNotifications']);

    Route::get('unread-notifications-count/{id}', [App\Http\Controllers\Api\NotificationController::class, 'getCountOfUnreadNotifications']);
    Route::post('update-notifications/{id}', [App\Http\Controllers\Api\NotificationController::class, 'updateNotification']);

    Route::post('notification-config-store', [App\Http\Controllers\Api\OptionController::class, 'notificationConfigStore']);
    Route::get('get-options/{id}', [App\Http\Controllers\Api\OptionController::class, 'getOptions']);

    Route::get('total-raised-funds/{id}', [App\Http\Controllers\Api\StartupController::class, 'getTotalCountOfFund']);
    Route::get('total-units/{id}', [App\Http\Controllers\Api\StartupController::class, 'getTotalCountOfUnits']);
    Route::post('payment', [App\Http\Controllers\Api\PaymentController::class, 'savePayment']);

    Route::post('upload-documents', [App\Http\Controllers\Api\DocumentsController::class, 'upload_documents']);
    Route::get('get-documents/{id}', [App\Http\Controllers\Api\DocumentsController::class, 'get_documents']);

    Route::post('selected-options-document-upload', [App\Http\Controllers\Api\DocumentsController::class, 'SelectedOptionsDocumentUpload']);
    Route::get('get-upload-documents-by-documenttype', [App\Http\Controllers\Api\DocumentsController::class, 'getUploadDocumentsByDocumentType']);

    Route::post('investor-viewer/{business_id}/{user_id}', [App\Http\Controllers\Api\NotificationController::class, 'investor_viewer']);
    Route::get('/get-all-errorlog', [App\Http\Controllers\Api\ErrorLogController::class, 'getErrorLog']);
    Route::post('/delete-error-log', [App\Http\Controllers\Api\ErrorLogController::class, 'deleteErrorLog']);
    Route::post('/delete-all-error-log', [App\Http\Controllers\Api\ErrorLogController::class, 'deleteAllErorlog']);
    Route::post('ifinworth-details', [App\Http\Controllers\Api\StartupController::class, 'insert_ifinworth_details']);
    Route::get('get-ifinworth-details/{id}', [App\Http\Controllers\Api\StartupController::class, 'get_startup_ifinworth_detail']);
    Route::post('add-pre-commited-investor', [App\Http\Controllers\Api\StartupController::class, 'add_pre_commited_investor']);    
    Route::post('delete-pre-commited-investor/{id}', [App\Http\Controllers\Api\StartupController::class, 'delete_pre_commited_investor']);
    Route::get('get-pre-commited-investors/{id}', [App\Http\Controllers\Api\StartupController::class, 'get_pre_commited_investors']);
    Route::post('publish-ccsp-fund/{id}', [App\Http\Controllers\Api\StartupController::class, 'publish_ccsp_fund']);



    Route::post('admin-add-campaign-detail', [App\Http\Controllers\Api\AdminController::class, 'admin_add_campaign_detail']);
    Route::post('admin-add-campaign', [App\Http\Controllers\Api\AdminController::class, 'admin_add_campaign_details']);

    Route::post('admin-add-campany-data', [App\Http\Controllers\Api\AdminController::class, 'admin_add_company_data']);
    Route::post('admin-add-team-members', [App\Http\Controllers\Api\AdminController::class, 'admin_add_team_members']);

    Route::get('/get-all-company-data', [App\Http\Controllers\Api\AdminController::class, 'get_all_company_data']);

    Route::get('/get-all-team-data', [App\Http\Controllers\Api\AdminController::class, 'get_all_team_data']);


    Route::post('admin-update-campany-data', [App\Http\Controllers\Api\AdminController::class, 'admin_update_company_data']);

    Route::post('admin-update-team-data', [App\Http\Controllers\Api\AdminController::class, 'admin_update_team_data']);

    Route::get('/get-investor-page-data', [App\Http\Controllers\Api\AdminController::class, 'get_investor_page_data']);
    Route::get('/get-team-and-company-data', [App\Http\Controllers\Api\AdminController::class, 'get_team_and_company_data']);

    Route::get('/get-all-product-data', [App\Http\Controllers\Api\AdminController::class, 'get_all_product_data']);

    Route::post('admin-add-round-details', [App\Http\Controllers\Api\AdminController::class, 'admin_add_round_details']);
    Route::post('admin-add-products', [App\Http\Controllers\Api\AdminController::class, 'admin_add_products']);
    Route::post('admin-update-products', [App\Http\Controllers\Api\AdminController::class, 'admin_update_product']);


    Route::post('company-delete/{id}', [App\Http\Controllers\Api\AdminController::class, 'destroy_admin_company_data']);

    Route::post('update-campign-status/{id}', [App\Http\Controllers\Api\AdminController::class, 'updateCampignStatus']);
    Route::post('delete-campign-status/{id}', [App\Http\Controllers\Api\AdminController::class, 'deleteCampign']);

    Route::get('get-all-campaign', [App\Http\Controllers\Api\AdminController::class, 'get_all_campaign']);


    Route::group(['prefix' => 'pan'], function () {	   		 					
        Route::post('/verification', [App\Http\Controllers\Api\PanVerificationController::class, 'panVerification']);
    });
    Route::post('/get-admin-message-data', [App\Http\Controllers\Api\AdminChatController::class, 'get_admin_message_data']);
    Route::post('/contact-by-admin-to-user-and-chef', [App\Http\Controllers\Api\AdminChatController::class, 'contact_by_admin_to_user_and_chef']);
    Route::post('/get-click-admin-chef-user-chat-data', [App\Http\Controllers\Api\AdminChatController::class, 'get_click_admin_chef_user_chat_data']);
    Route::post('/contact-by-admin-to-user-and-chef-with-share-file', [App\Http\Controllers\Api\AdminChatController::class, 'contact_by_admin_to_user_and_chef_with_share_file']);
    Route::get('get-all-user-data', [App\Http\Controllers\Api\AdminChatController::class, 'get_all_user_data']);
    Route::post('/send-message-to-user-by-admin', [App\Http\Controllers\Api\AdminChatController::class, 'send_message_to_user_by_admin']);
    Route::post('/create-group-by-admin', [App\Http\Controllers\Api\AdminChatController::class, 'create_group_by_admin']);


    Route::post('/get-startup-message-data', [App\Http\Controllers\Api\StartupChatController::class, 'get_startup_message_data']);
    Route::post('/contact-user-by-startup', [App\Http\Controllers\Api\StartupChatController::class, 'contact_user_by_startup']);
    Route::post('/get-click-startup-user-chat-data', [App\Http\Controllers\Api\StartupChatController::class, 'get_click_startup_user_chat_data']);
    Route::post('/contact-user-by-startup-with-share-file', [App\Http\Controllers\Api\StartupChatController::class, 'contact_user_by_startup_with_share_file']);
    Route::get('/get-admin-data', [App\Http\Controllers\Api\StartupChatController::class, 'get_admin_data']);

    Route::post('/get-investor-message-data', [App\Http\Controllers\Api\InvestorChatController::class, 'get_investor_message_data']);
    Route::post('/contact-user-by-investor', [App\Http\Controllers\Api\InvestorChatController::class, 'contact_user_by_investor']);
    Route::post('/get-click-investor-user-chat-data', [App\Http\Controllers\Api\InvestorChatController::class, 'get_click_investor_user_chat_data']);
    Route::post('/contact-user-by-investor-with-share-file', [App\Http\Controllers\Api\InvestorChatController::class, 'contact_user_by_investor_with_share_file']);
    Route::get('/get-admin-data-investor', [App\Http\Controllers\Api\InvestorChatController::class, 'get_admin_data_investor']);
    Route::post('/create-group-by-investor', [App\Http\Controllers\Api\InvestorChatController::class, 'create_group_by_investor']);
    Route::get('/get-single-group-data', [App\Http\Controllers\Api\InvestorChatController::class, 'get_single_group_data']);
});
