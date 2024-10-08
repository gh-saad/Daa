<?php

use App\Http\Controllers\HelpdeskTicketController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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



require __DIR__.'/auth.php';

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');
// Route::get('/home', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('home');

// Route::get('/test_middleware', function () {
//     return "2FA middleware work!";
// })->middleware(['auth', '2fa']);

    Route::get('/register/{lang?}', 'Auth\RegisteredUserController@create')->name('register');
    Route::get('/login/{lang?}', 'Auth\AuthenticatedSessionController@create')->name('login');
    Route::get('/forgot-password/{lang?}', 'Auth\PasswordResetLinkController@create')->name('password.request');
    Route::get('/verify-email/{lang?}', 'Auth\EmailVerificationPromptController@__invoke')->name('verification.notice');

    Route::get('/', 'HomeController@index')->name('start');

    // module page before login
    Route::get('add-on', 'HomeController@Software')->name('apps.software');
    Route::get('add-on/details/{slug}', 'HomeController@SoftwareDetails')->name('software.details');
    Route::get('pricing', 'HomeController@Pricing')->name('apps.pricing');
Route::group(['middleware' => ['verified', 'check.contract.status']], function ()
{
    if(module_is_active('GoogleAuthentication'))
    {
        Route::get('/dashboard', 'HomeController@Dashboard')->name('dashboard')->middleware(
            [
                'auth',
                '2fa',
            ]
        );
        Route::get('/home', 'HomeController@Dashboard')->name('home')->middleware(
            [
                'auth',
                '2fa',
            ]
        );
    }
    else
    {
        Route::get('/dashboard', 'HomeController@Dashboard')->name('dashboard')->middleware(
            [
                'auth',
            ]
        );
        Route::get('/home', 'HomeController@Dashboard')->name('home')->middleware(
            [
                'auth',
            ]
        );
    }

    Route::resource('users', 'UserController')->middleware(['auth']);
    Route::get('users/{id}/login-with-company', 'UserController@LoginWithCompany')->name('login.with.company');
    Route::get('company-info/{id}', 'UserController@CompnayInfo')->name('company.info');
    Route::get('login-with-company/exit', 'UserController@ExitCompany')->name('exit.company');
    Route::any('user-reset-password/{id}', 'UserController@UserPassword')->name('users.reset')->middleware(
        [
            'auth',
        ]
    );
    Route::post('user-reset-password/{id}', 'UserController@UserPasswordReset')->name('user.password.update')->middleware(
        [
            'auth',
        ]
    );
    Route::get('user-login/{id}', 'UserController@LoginManage')->name('users.login')->middleware(
        [
            'auth',
        ]
    );
    Route::get('users/list/view', 'UserController@List')->name('users.list.view')->middleware(['auth']);
    Route::post('user-unable', 'UserController@UserUnable')->name('user.unable')->middleware(['auth']);
    Route::get('profile', 'UserController@profile')->name('profile')->middleware(['auth']);
    Route::post('edit-profile', 'UserController@editprofile')->name('edit.profile')->middleware(
        [
            'auth',
        ]
    );
    Route::post('change-password', 'UserController@updatePassword')->name('update.password')->middleware(
        [
            'auth',
        ]
    );

    // users import
    Route::get('users/import/export', 'UserController@fileImportExport')->name('users.file.import');
    Route::post('users/import', 'UserController@fileImport')->name('users.import');
    Route::get('users/import/modal', 'UserController@fileImportModal')->name('users.import.modal');
    Route::post('users/data/import/', 'UserController@UserImportdata')->name('users.import.data');

    //User Log
    Route::get('users/logs/history', 'UserController@UserLogHistory')->name('users.userlog.history')->middleware(
        [
            'auth',
        ]
    );
    Route::get('users/logs/{id}', 'UserController@UserLogView')->name('users.userlog.view')->middleware(
        [
            'auth',
        ]
    );
    Route::delete('users/logs/destroy/{id}', 'UserController@UserLogDestroy')->name('users.userlog.destroy')->middleware(
        [
            'auth',
        ]
    );

    Route::resource('roles', 'RoleController')->middleware(['auth']);
    Route::resource('permissions', 'PermissionController')->middleware(['auth']);


    // Plans
    Route::resource('plans', 'PlanController')->middleware(['auth']);

    Route::get('plan/list', 'PlanController@PlanList')->name('plan.list')->middleware(['auth']);
    Route::post('plan/store', 'PlanController@PlanStore')->name('plan.store')->middleware(['auth']);
    Route::get('plan/active', 'PlanController@ActivePlans')->name('active.plans')->middleware(['auth']);
    Route::any('plan/package-data', 'PlanController@PackageData')->name('package.data')->middleware(['auth']);
    Route::get('plan/plan-buy/{id}', 'PlanController@PlanBuy')->name('plan.buy')->middleware(['auth']);
    Route::get('plan/plan-trial/{id}', 'PlanController@PlanTrial')->name('plan.trial')->middleware(['auth']);
    Route::get('plan/payment/{id}', 'PlanController@payment')->name('plan.payment')->middleware(['auth']);

    Route::get('plan/order', 'PlanController@orders')->name('plan.order.index')->middleware(['auth']);

    Route::get('add-one/detail/{id}', 'PlanController@AddOneDetail')->name('add-one.detail')->middleware(['auth']);
    Route::post('add-one/detail/save/{id}', 'PlanController@AddOneDetailSave')->name('add-one.detail.save')->middleware(['auth']);


    // Email Templates
    Route::resource('email-templates', 'EmailTemplateController')->middleware(['auth']);
    Route::get('email_template_lang/{id}/{lang?}', 'EmailTemplateController@show')->name('manage.email.language')->middleware(['auth']);
    Route::put('email_template_store/{pid}', 'EmailTemplateController@storeEmailLang')->name('store.email.language')->middleware(['auth']);
    Route::put('email_template_status/{id}', 'EmailTemplateController@updateStatus')->name('status.email.language')->middleware(['auth']);
    Route::resource('email_template', 'EmailTemplateController')->middleware(['auth']);
    // End Email Templates


    // Module Install
    Route::get('modules/list', 'ModuleController@index')->name('module.index')->middleware(['auth']);
    Route::get('modules/add', 'ModuleController@add')->name('module.add')->middleware(['auth']);
    Route::post('install-modules', 'ModuleController@install')->name('module.install')->middleware(['auth']);
    Route::post('remove-modules/{module}', 'ModuleController@remove')->name('module.remove')->middleware(['auth']);
    Route::post('modules-enable', 'ModuleController@enable')->name('module.enable')->middleware(['auth']);
    Route::get('cancel/add-on/{name}', 'ModuleController@CancelAddOn')->name('cancel.add.on')->middleware(['auth']);
    // End Module Install


    // Workspace
    Route::resource('workspace', 'WorkSpaceController')->middleware(['auth']);
    Route::get('workspace/change/{id}', 'WorkSpaceController@change')->name('workspace.change')->middleware(['auth']);
    // End Workspace


    // Language
    Route::get('/lang/change/{lang}',['as' => 'lang.change','uses' =>'LanguageController@changeLang'])->middleware(['auth']);
    Route::get('langmanage/{lang?}/{module?}', [LanguageController::class, 'index'])->name('lang.index');
    Route::post('langs/{lang?}/{module?}', [LanguageController::class, 'storeData'])->name('lang.store.data');
    Route::post('disable-language',[LanguageController::class,'disableLang'])->name('disablelanguage')->middleware(['auth']);
    Route::get('create-language', [LanguageController::class, 'create'])->name('create.language');
    Route::any('store-language', [LanguageController::class, 'store'])->name('store.language');
    Route::delete('/lang/{id}', [LanguageController::class, 'destroy'])->name('lang.destroy');
    // End Language

    // helpdesk
    Route::resource('helpdesk', HelpdeskTicketController::class)->middleware(['auth']);
    Route::resource('helpdeskticket-category', 'HelpdeskTicketCategoryController')->middleware(['auth']);
    Route::get('helpdesk-tickets/search/{status?}',['as' => 'helpdesk-tickets.search','uses' =>'HelpdeskTicketController@index']);
    Route::post('helpdesk-ticket/getUser', 'HelpdeskTicketController@getUser')->name('helpdesk-tickets.getuser')->middleware(['auth']);

    Route::post('helpdesk-ticket/{id}/conversion',['as' => 'helpdesk-ticket.conversion.store','uses' =>'HelpdeskConversionController@store'])->middleware(['auth']);
    Route::delete('helpdesk-ticket-attachment/{tid}/destroy/{id}',['as' => 'helpdesk-ticket.attachment.destroy','uses' =>'HelpdeskTicketController@attachmentDestroy']);
    Route::post('helpdesk-ticket/{id}/note',['as' => 'helpdesk-ticket.note.store','uses' =>'HelpdeskTicketController@storeNote']);
    // End helpdesk

    // Setting
    Route::get('settings', 'SettingController@index')->name('settings.index')->middleware(['auth']);
    Route::post('settings-save', 'SettingController@store')->name('settings.save')->middleware(['auth']);
    Route::post('system-settings-save', 'SettingController@SystemStore')->name('system.settings.save')->middleware(['auth']);
    Route::post('company-setting-save', 'SettingController@CompanySettingStore')->name('company.setting.save')->middleware(['auth']);
    Route::post('storage-settings-save', 'SettingController@storageStore')->name('storage.setting.store')->middleware(['auth']);
    Route::post('mail-settings-save', 'SettingController@mailStore')->name('mail.setting.store')->middleware(['auth']);
    Route::post('test-settings-mail', 'SettingController@testMail')->name('test.setting.mail')->middleware(['auth']);
    Route::post('send-settings-mail', 'SettingController@sendTestMail')->name('send.test.mail')->middleware(['auth']);
    Route::post('mail-notification-settings-save', 'SettingController@MailNotificationStore')->name('mail.notification.setting.store')->middleware(['auth']);
    Route::post('pusher-setting', 'SettingController@savePusherSettings')->name('pusher.setting')->middleware(['auth']);
    Route::post('seo/setting/save', 'SettingController@SeoSetting')->name('seo.setting.save')->middleware(['auth']);
    Route::post('cookie/setting/save', 'SettingController@CookieSetting')->name('cookie.setting.save')->middleware(['auth']);

    Route::post('bank-transfer-setting', 'BanktransferController@setting')->name('bank_transfer.setting')->middleware(['auth']);

    Route::post('/bank/transfer/pay', 'BanktransferController@planPayWithBank')->name('plan.pay.with.bank')->middleware(['auth']);
    // Coupon
    Route::resource('coupons', 'CouponController')->middleware(['auth']);
    Route::get('/apply-coupon','CouponController@applyCoupon')->name('apply.coupon')->middleware(['auth']);

    Route::resource('bank-transfer-request', 'BanktransferController')->middleware(['auth']);
    Route::get('invoice-bank-request/{id}','BanktransferController@invoiceBankRequestEdit')->name('invoice.bank.request.edit')->middleware(['auth']);
    Route::post('bank-transfer-request-edit/{id}','BanktransferController@invoiceBankRequestupdate')->name('invoice.bank.request.update')->middleware(['auth']);

    Route::post('ai/key/setting/save', 'SettingController@AiKeySettingSave')->name('ai.key.setting.save')->middleware(['auth']);

    // Constant
    Route::post('check-multi-upload', 'SettingController@uploads')->name('check.multi.upload')->middleware(['auth']);

    // only allowed to super admin and one selected company
    Route::middleware(['allowed.users'])->group(function () {
        // dealer
        Route::get('/dealers/edit/{id}', 'DealersController@edit')->name('backend.dealers.edit')->middleware(['auth']);
        Route::get('/dealers/view/{id}', 'DealersController@view')->name('backend.dealers.view')->middleware(['auth']);
        Route::get('/dealers/create', 'DealersController@create')->name('backend.dealers.create')->middleware(['auth']);
        Route::delete('/dealers/delete/{id}', 'DealersController@destroy')->name('backend.dealers.destroy')->middleware(['auth']);
        Route::post('/dealers/update/{id}', 'DealersController@update')->name('backend.dealers.update')->middleware(['auth']);
        Route::post('/dealers/store', 'DealersController@store')->name('backend.dealers.store')->middleware(['auth']);

        // show all
        Route::get('/dealers', 'DealersController@grid')->name('backend.dealers.grid');
        Route::get('/dealers/list', 'DealersController@list')->name('backend.dealers.list');
        // show accepted
        Route::get('/dealers/accepted', 'DealersController@accepted_grid')->name('backend.dealers.accepted.grid');
        Route::get('/dealers/accepted/list', 'DealersController@accepted_list')->name('backend.dealers.accepted.list');
        // show denied
        Route::get('/dealers/denied', 'DealersController@denied_grid')->name('backend.dealers.denied.grid');
        Route::get('/dealers/denied/list', 'DealersController@denied_list')->name('backend.dealers.denied.list');
    });

    Route::group(['middleware' => 'PlanModuleCheck:Account-Taskly'], function ()
    {
            // invoice
        Route::post('invoice/customer', 'InvoiceController@customer')->name('invoice.customer')->middleware(
            [
                'auth'
            ]
        );
        Route::post('invoice-attechment/{id}', 'InvoiceController@invoiceAttechment')->name('invoice.file.upload')->middleware(
            [
                'auth'
            ]
        );
        Route::delete('invoice-attechment/destroy/{id}', 'InvoiceController@invoiceAttechmentDestroy')->name('invoice.attachment.destroy')->middleware(
            [
                'auth'
            ]
        );
        Route::post('invoice/product', 'InvoiceController@product')->name('invoice.product')->middleware(
            [
                'auth'
            ]
        );
        Route::get('invoice/{id}/duplicate', 'InvoiceController@duplicate')->name('invoice.duplicate')->middleware(
            [
                'auth'
            ]
        );
        Route::get('invoice/items', 'InvoiceController@items')->name('invoice.items')->middleware(
            [
                'auth'
            ]
        );
        Route::post('invoice/product/destroy', 'InvoiceController@productDestroy')->name('invoice.product.destroy')->middleware(
            [
                'auth'
            ]
        );
        Route::get('invoice/grid/view', 'InvoiceController@Grid')->name('invoice.grid.view')->middleware(
            [
                'auth'
            ]
        );
        Route::resource('invoice', 'InvoiceController')->middleware(
            [
                'auth'
            ]
        );

        Route::get('invoice/create/{cid}', 'InvoiceController@create')->name('invoice.create')->middleware(
            [
                'auth'
            ]
        );
        Route::get('/invoice/pay/{invoice}', ['as' => 'pay.invoice','uses' => 'InvoiceController@payinvoice',])->middleware(
            [
                'auth'
            ]
        );
        Route::get('invoice/{id}/sent', 'InvoiceController@sent')->name('invoice.sent')->middleware(
            [
                'auth'
            ]
        );
        Route::get('invoice/{id}/resent', 'InvoiceController@resent')->name('invoice.resent')->middleware(
            [
                'auth'
            ]
        );

        Route::get('invoice/{id}/payment/reminder', 'InvoiceController@paymentReminder')->name('invoice.payment.reminder')->middleware(
            [
                'auth'
            ]
        );
        Route::get('invoice/pdf/{id}', 'InvoiceController@invoice')->name('invoice.pdf');
        Route::get('invoice/{id}/payment', 'InvoiceController@payment')->name('invoice.payment')->middleware(
            [
                'auth'
            ]
        );
        Route::post('invoice/{id}/payment', 'InvoiceController@createPayment')->name('invoice.payment')->middleware(
            [
                'auth'
            ]
        );
        Route::post('invoice/{id}/payment/{pid}/', 'InvoiceController@paymentDestroy')->name('invoice.payment.destroy')->middleware(
            [
                'auth'
            ]
        );
        Route::get('invoice/{id}/send', 'InvoiceController@customerInvoiceSend')->name('invoice.send')->middleware(
            [
                'auth',
            ]
        );
        Route::post('invoice/{id}/send/mail', 'InvoiceController@customerInvoiceSendMail')->name('invoice.send.mail')->middleware(
            [
                'auth',
            ]
        );
        Route::post('invoice/section/type', 'InvoiceController@InvoiceSectionGet')->name('invoice.section.type')->middleware(
            [
                'auth',
            ]
        );
        Route::get('delivery-form/pdf/{id}', 'InvoiceController@pdf')->name('delivery-form.pdf')->middleware(
            [
                'auth',
            ]
        );


        // Proposal
        Route::post('proposal-attechment/{id}', 'ProposalController@proposalAttechment')->name('proposal.file.upload')->middleware(
            [
                'auth'
            ]
        );
        Route::delete('proposal-attechment/destroy/{id}', 'ProposalController@proposalAttechmentDestroy')->name('proposal.attachment.destroy')->middleware(
            [
                'auth'
            ]
        );
        Route::post('proposal/customer', 'ProposalController@customer')->name('proposal.customer')->middleware(
            [
                'auth'
            ]
        );
        Route::post('proposal/product', 'ProposalController@product')->name('proposal.product')->middleware(
            [
                'auth'
            ]
        );
        Route::get('proposal/{id}/convert', 'ProposalController@convert')->name('proposal.convert')->middleware(
            [
                'auth'
            ]
        );
        Route::get('proposal/{id}/duplicate', 'ProposalController@duplicate')->name('proposal.duplicate')->middleware(
            [
                'auth'
            ]
        );
        Route::get('proposal/items', 'ProposalController@items')->name('proposal.items')->middleware(
            [
                'auth'
            ]
        );
        Route::post('proposal/product/destroy', 'ProposalController@productDestroy')->name('proposal.product.destroy')->middleware(
            [
                'auth'
            ]
        );
        Route::resource('proposal', 'ProposalController')->middleware(
            [
                'auth'
            ]
        );
        Route::get('proposal/grid/view', 'ProposalController@Grid')->name('proposal.grid.view')->middleware(
            [
                'auth'
            ]
        );
        Route::get('proposal/create/{cid}', 'ProposalController@create')->name('proposal.create')->middleware(
            [
                'auth'
            ]
        );

        Route::get('proposal/{id}/status/change', 'ProposalController@statusChange')->name('proposal.status.change')->middleware(
            [
                'auth'
            ]
        );
        Route::get('proposal/{id}/resent', 'ProposalController@resent')->name('proposal.resent')->middleware(
            [
                'auth'
            ]
        );
        Route::post('proposal/section/type', 'ProposalController@ProposalSectionGet')->name('proposal.section.type')->middleware(
            [
                'auth',
            ]
        );
        Route::get('proposal/{id}/sent', 'ProposalController@sent')->name('proposal.sent');
    });
});
// invoices template setting save
Route::post('/invoices/template/setting', ['as' => 'invoice.template.setting','uses' => 'InvoiceController@saveTemplateSettings'])->middleware(['auth']);
Route::get('/invoices/preview/{template}/{color}', ['as' => 'invoice.preview','uses' => 'InvoiceController@previewInvoice'])->middleware(['auth']);

Route::get('/proposal/preview/{template}/{color}', ['as' => 'proposal.preview','uses' => 'ProposalController@previewInvoice'])->middleware(['auth']);
Route::post('/proposal/template/setting', ['as' => 'proposal.template.setting','uses' => 'ProposalController@saveTemplateSettings'])->middleware(['auth']);
Route::get('/proposal/pay/{proposal}', ['as' => 'pay.proposalpay','uses' => 'ProposalController@payproposal']);
Route::get('proposal/pdf/{id}', 'ProposalController@proposal')->name('proposal.pdf');
Route::get('/invoice/pay/{invoice}', ['as' => 'pay.invoice','uses' => 'InvoiceController@payinvoice',]);
Route::get('invoice/pdf/{id}', 'InvoiceController@invoice')->name('invoice.pdf');

Route::post('guest/module/selection', 'ModuleController@GuestModuleSelection')->name('guest.module.selection');
Route::get('module/reset', 'ModuleController@ModuleReset')->name('module.reset');

Route::post('/bank/transfer/invoice', 'BanktransferController@invoicePayWithBank')->name('invoice.pay.with.bank');

// cookie
Route::get('cookie/consent', 'SettingController@CookieConsent')->name('cookie.consent');

Route::get('/config-cache', function()
{
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return redirect()->back()->with('success', 'Cache Clear Successfully');

})->name('config.cache');

Route::post('helpdesk-ticket/{id}', ['as' => 'helpdesk-ticket.reply','uses' =>'HelpdeskTicketController@reply']);
Route::get('helpdesk-ticket-show/{id}', [HelpdeskTicketController::class, 'show'])->name('helpdesk.view');

// Added by Saad
Route::get('synchronizer/get-data',  'SynchronizerController@get_data')->name('synchronizer.get-data');

// place this outside the middleware that verifies contract status
Route::get('/logout-without-contract', 'UserController@logout_new')->name('logout.new');

// contract approval routes
Route::post('/approval/submit', 'ApprovalController@submit')->name('approval.submit');

// currency change (NOT USED NOW)
Route::get('/currency/header/change/{code}', 'SettingController@currencyHeaderChange')->name('currency.header.change')->middleware(['auth']);

// currency rate change
Route::post('/currency/rate/change', 'SettingController@currencyRateChange')->name('currency.rate.change')->middleware(['auth']);

// get customer details
Route::get('/get/customer/{id}', [\Modules\Account\Http\Controllers\CustomerController::class, 'getCustomerDetails'])->name('get.customer.details');


Route::get('/expence/cash', 'ExpanceController@cash')->name('expence.cash');
Route::get('/expence/bank', 'ExpanceController@bank')->name('expence.bank');