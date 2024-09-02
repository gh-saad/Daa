<?php

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
use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\PurchaseController;
use Modules\Account\Http\Controllers\PurchaseDebitNoteController;
use Modules\Account\Http\Controllers\WarehouseController;
use Modules\Account\Http\Controllers\WarehouseTransferController;

Route::group(['middleware' => 'PlanModuleCheck:Account'], function ()
    {
    Route::prefix('account')->group(function() {
        Route::get('/', 'AccountController@index');
    });
    // dashboard
    Route::get('dashboard/account',['as' => 'dashboard.account','uses' =>'AccountController@index'])->middleware(['auth']);




    // Bank account
    Route::resource('bank-account', 'BankAccountController')->middleware(
        [
            'auth'
        ]
    );

    //chart-of-account
    Route::resource('chart-of-account', 'ChartOfAccountController')->middleware(['auth']);
    Route::post('chart-of-account/subtype', 'ChartOfAccountController@getSubType')->name('charofAccount.subType')->middleware(['auth']);

    // Transfer
    Route::resource('bank-transfer', 'TransferController')->middleware(
        [
            'auth'
        ]
    );

    // customer
    Route::resource('customer', 'CustomerController')->middleware(
        [
            'auth'
        ]
    );
    Route::get('customer-grid', 'CustomerController@grid')->name('customer.grid')->middleware(
        [
            'auth'
        ]
    );

    Route::ANY('customer/{id}/statement', 'CustomerController@statement')->name('customer.statement')->middleware(
        [
            'auth'
        ]
    );

    // Customer import
     Route::get('customer/import/export', 'CustomerController@fileImportExport')->name('customer.file.import')->middleware(['auth']);
     Route::post('customer/import', 'CustomerController@fileImport')->name('customer.import')->middleware(['auth']);
     Route::get('customer/import/modal', 'CustomerController@fileImportModal')->name('customer.import.modal')->middleware(['auth']);
     Route::post('customer/data/import/', 'CustomerController@customerImportdata')->name('customer.import.data')->middleware(['auth']);


    // Vendor
    Route::resource('vendors', 'VenderController')->middleware(
        [
            'auth'
        ]
    );
    Route::get('vendors-grid', 'VenderController@grid')->name('vendors.grid')->middleware(
        [
            'auth'
        ]
    );
    Route::ANY('vendors/{id}/statement', 'VenderController@statement')->name('vendor.statement')->middleware(
        [
            'auth'
        ]
    );

     // Vendor import
     Route::get('vendor/import/export', 'VenderController@fileImportExport')->name('vendor.file.import')->middleware(['auth']);
     Route::post('vendor/import', 'VenderController@fileImport')->name('vendor.import')->middleware(['auth']);
     Route::get('vendor/import/modal', 'VenderController@fileImportModal')->name('vendor.import.modal')->middleware(['auth']);
     Route::post('vendor/data/import/', 'VenderController@vendorImportdata')->name('vendor.import.data')->middleware(['auth']);

    Route::post('vendor/quick/add', 'VenderController@add_quick_vendor')->name('add.quick.vendor')->middleware(['auth']);

    // credit note
    Route::get('invoice/{id}/credit-note', 'CreditNoteController@create')->name('invoice.credit.note')->middleware(
        [
            'auth'
        ]
    );
    Route::post('invoice/{id}/credit-note', 'CreditNoteController@store')->name('invoice.credit.note')->middleware(
        [
            'auth'
        ]
    );
    Route::get('invoice/{id}/credit-note/edit/{cn_id}', 'CreditNoteController@edit')->name('invoice.edit.credit.note')->middleware(
        [
            'auth'
        ]
    );
    Route::post('invoice/{id}/credit-note/edit/{cn_id}', 'CreditNoteController@update')->name('invoice.edit.credit.note')->middleware(
        [
            'auth'
        ]
    );
    Route::delete('invoice/{id}/credit-note/delete/{cn_id}', 'CreditNoteController@destroy')->name('invoice.delete.credit.note')->middleware(
        [
            'auth'
        ]
    );

    // revenue
    Route::resource('revenue', 'RevenueController')->middleware(
        [
            'auth',
        ]
    );

    // bill payment
    Route::resource('payment', 'PaymentController')->middleware(
        [
            'auth',
        ]
    );
    Route::post('bill-attechment/{id}', 'BillController@billAttechment')->name('bill.file.upload')->middleware(
        [
            'auth'
        ]
    );
    Route::delete('bill-attechment/destroy/{id}', 'BillController@billAttechmentDestroy')->name('bill.attachment.destroy')->middleware(
        [
            'auth'
        ]
    );
    Route::post('bill/vendors', 'BillController@vendor')->name('bill.vendor')->middleware(
        [
            'auth',
        ]
    );
    Route::post('bill/product', 'BillController@product')->name('bill.product')->middleware(
        [
            'auth',
        ]
    );
    Route::get('bill/items', 'BillController@items')->name('bill.items')->middleware(
        [
            'auth',
        ]
    );
    Route::resource('bill', 'BillController')->middleware(
        [
            'auth',
        ]
    );
    Route::get('bill-grid', 'BillController@grid')->name('bill.grid')->middleware(
        [
            'auth'
        ]
    );
    Route::get('bill/create/{cid}', 'BillController@create')->name('bill.create')->middleware(
        [
            'auth',
        ]
    );
    Route::post('bill/product/destroy', 'BillController@productDestroy')->name('bill.product.destroy')->middleware(
        [
            'auth',
        ]
    );
    Route::get('bill/{id}/duplicate', 'BillController@duplicate')->name('bill.duplicate')->middleware(
        [
            'auth',
        ]
    );
    Route::get('bill/{id}/sent', 'BillController@sent')->name('bill.sent')->middleware(
        [
            'auth',
        ]
    );
    Route::get('bill/{id}/payment', 'BillController@payment')->name('bill.payment')->middleware(
        [
            'auth',
        ]
    );
    Route::post('bill/{id}/payment', 'BillController@createPayment')->name('bill.payment')->middleware(
        [
            'auth',
        ]
    );
    Route::post('bill/{id}/payment/{pid}/destroy', 'BillController@paymentDestroy')->name('bill.payment.destroy')->middleware(
        [
            'auth',
        ]
    );
    Route::get('bill/{id}/resent', 'BillController@resent')->name('bill.resent')->middleware(
        [
            'auth',
        ]
    );
    Route::post('bill/section/type', 'BillController@BillSectionGet')->name('bill.section.type')->middleware(
        [
            'auth',
        ]
    );
    Route::get('bill/{id}/debit-note', 'DebitNoteController@create')->name('bill.debit.note')->middleware(
        [
            'auth',
        ]
    );
    Route::post('bill/{id}/debit-note', 'DebitNoteController@store')->name('bill.debit.note')->middleware(
        [
            'auth',
        ]
    );
    Route::get('bill/{id}/debit-note/edit/{cn_id}', 'DebitNoteController@edit')->name('bill.edit.debit.note')->middleware(
        [
            'auth',
        ]
    );
    Route::post('bill/{id}/debit-note/edit/{cn_id}', 'DebitNoteController@update')->name('bill.edit.debit.note')->middleware(
        [
            'auth',
        ]
    );
    Route::delete('bill/{id}/debit-note/delete/{cn_id}', 'DebitNoteController@destroy')->name('bill.delete.debit.note')->middleware(
        [
            'auth',
        ]
    );

    // settig in account
    Route::post('/accountss/setting/store', 'AccountController@setting')->name('accounts.setting.save')->middleware(['auth']);

    Route::get('/bill/vendor/{id}', 'BillController@getVendorDetails')->name('bill.vendor.details');

    // bill template settig in account

    Route::get('/bill/preview/{template}/{color}', ['as' => 'bill.preview','uses' => 'BillController@previewBill',]);

    Route::post('/account/setting/store', 'BillController@saveBillTemplateSettings')->name('bill.template.setting')->middleware(['auth']);

    // Account Report
    Route::get('report/supplier', 'ReportController@supplier_report')->name('supplier.report')->middleware(['auth']);
    Route::get('report/transaction', 'TransactionController@index')->name('transaction.index')->middleware(['auth']);
    Route::get('report/account-statement-report', 'ReportController@accountStatement')->name('report.account.statement')->middleware(['auth']);
    Route::get('report/income-summary', 'ReportController@incomeSummary')->name('report.income.summary')->middleware(['auth']);
    Route::get('report/expense-summary', 'ReportController@expenseSummary')->name('report.expense.summary')->middleware(['auth']);
    Route::get('report/income-vs-expense-summary', 'ReportController@incomeVsExpenseSummary')->name('report.income.vs.expense.summary')->middleware(['auth']);
    Route::get('report/tax-summary', 'ReportController@taxSummary')->name('report.tax.summary')->middleware(['auth']);
    Route::get('report/profit-loss-summary', 'ReportController@profitLossSummary')->name('report.profit.loss.summary')->middleware(['auth']);
    Route::get('report/invoice-summary', 'ReportController@invoiceSummary')->name('report.invoice.summary')->middleware(['auth']);
    Route::get('report/bill-summary', 'ReportController@billSummary')->name('report.bill.summary')->middleware(['auth']);
    Route::get('report/product-stock-report', 'ReportController@productStock')->name('report.product.stock.report')->middleware(['auth']);
    });
    Route::get('/bill/pay/{bill}', ['as' => 'pay.billpay','uses' => 'BillController@paybill']);
    Route::get('bill/pdf/{id}', 'BillController@bill')->name('bill.pdf');
    Route::get('bill/{id}/send', 'BillController@venderBillSend')->name('vendor.bill.send');
    Route::post('bill/{id}/send/mail', 'BillController@venderBillSendMail')->name('vendor.bill.send.mail');

    // Purchase
    Route::resource('purchase', 'PurchaseController');
    Route::get('purchase-grid', 'PurchaseController@grid')->name('purchase.grid');
    Route::get('purchase/create/{cid}', 'PurchaseController@create')->name('purchase.create');
    Route::post('purchase/{id}/payment/{pid}/destroy', 'PurchaseController@paymentDestroy')->name('purchase.payment.destroy');
    Route::post('purchase/product/destroy', 'PurchaseController@productDestroy')->name('purchase.product.destroy');
    Route::post('purchase/product', 'PurchaseController@product')->name('purchase.product');
    Route::post('purchase/vender', 'PurchaseController@vender')->name('purchase.vender');
    Route::get('purchase/{id}/sent', 'PurchaseController@sent')->name('purchase.sent');
    Route::get('purchase/{id}/resent', 'PurchaseController@resent')->name('purchase.resent');
    Route::get('purchase/pdf/{id}', 'PurchaseController@purchase')->name('purchase.pdf');
    Route::post('purchase/items', 'PurchaseController@items')->name('purchase.items');
    Route::get('purchase/{id}/payment', 'PurchaseController@payment')->name('purchase.payment');
    Route::post('purchase/{id}/payment', 'PurchaseController@createPayment')->name('purchase.payment');
    
    Route::get('purchase/{id}/debit-note', 'PurchaseDebitNoteController@create')->name('purchase.debit.note')->middleware(['auth',]);
    Route::post('purchase/{id}/debit-note', 'PurchaseDebitNoteController@store')->name('purchase.debit.note')->middleware(['auth',]);
    Route::get('purchase/{id}/debit-note/edit/{cn_id}', 'PurchaseDebitNoteController@edit')->name('purchase.edit.debit.note')->middleware(['auth',]);
    Route::post('purchase/{id}/debit-note/edit/{cn_id}', 'PurchaseDebitNoteController@update')->name('purchase.edit.debit.note')->middleware(['auth',]);
    Route::delete('purchase/{id}/debit-note/delete/{cn_id}', 'PurchaseDebitNoteController@destroy')->name('purchase.delete.debit.note')->middleware(['auth',]);

    Route::get('/vendor/purchase/{id}/', 'PurchaseController@purchaseLink')->name('purchase.link.copy');
    Route::get('/vendor/bill/{id}/', 'PurchaseController@invoiceLink')->name('bill.link.copy')->middleware(['auth']);
    Route::post('purchase/{id}/file',['as' => 'purchases.file.upload','uses' =>'PurchaseController@fileUpload'])->middleware(['auth']);
    Route::delete("purchase/{id}/destroy", 'PurchaseController@fileUploadDestroy')->name("purchase.attachment.destroy")->middleware(['auth']);

    // Warehouse
    Route::resource('warehouse', 'WarehouseController')->middleware(
        [
            'auth',
        ]
    );
    
    //warehouse import
    Route::get('warehouse/import/export', 'WarehouseController@fileImportExport')->name('warehouse.file.import')->middleware(['auth']);
    Route::post('warehouse/import', 'WarehouseController@fileImport')->name('warehouse.import')->middleware(['auth']);
    Route::get('warehouse/import/modal', 'WarehouseController@fileImportModal')->name('warehouse.import.modal')->middleware(['auth']);
    Route::post('warehouse/data/import/', 'WarehouseController@warehouseImportdata')->name('warehouse.import.data')->middleware(['auth']);

    Route::get('productservice/{id}/detail', 'WarehouseController@warehouseDetail')->name('productservice.detail');

    //warehouse-transfer
    Route::resource('warehouse-transfer', 'WarehouseTransferController')->middleware(['auth']);
    Route::post('warehouse-transfer/getproduct', 'WarehouseTransferController@getproduct')->name('warehouse-transfer.getproduct')->middleware(['auth']);
    Route::post('warehouse-transfer/getquantity', 'WarehouseTransferController@getquantity')->name('warehouse-transfer.getquantity')->middleware(['auth']);

    // add by jamal 02/09/2024
    Route::get('/get-vehicles-for-vendor/{vendorId}', 'VenderController@getVehiclesForVendor')->middleware(['auth']);
    Route::get('/get-product-details/{vehicleId}', 'VenderController@getVehicleDetails')->middleware(['auth']);