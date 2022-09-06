<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\IpdServiceController;
use App\Http\Controllers\OpdServiceController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\ProductBrandController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\PathologyController;
use App\Http\Controllers\DoctorsSpecialityController;
use App\Http\Controllers\ProductModelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IpdServicePackageController;
use App\Http\Controllers\OpdServicePackageController;
use App\Http\Controllers\PatientAdmissionController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\CorporateClientController;
use App\Http\Controllers\CorporateClientDiscountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\OpdBillingController;
use App\Http\Controllers\IpdBillingController;
use App\Http\Controllers\DepartmentConsumptionController;
use App\Http\Controllers\MedicineConsumptionController;
use App\Http\Controllers\ReferralSettingController;
use App\Http\Controllers\ReferralController;

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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth:sanctum', 'verified'])->get('dashboard', [DashboardController::class, 'dashboard']);

Route::group(['middleware' => 'auth'], function () {

    /* #################### Start User Management #################### */
    // show change password form
    Route::get('change-password', [UserController::class, 'changePassword'])->name('changePassword');
    // update logged in user password
    Route::post('updatePassword', [UserController::class, 'updatePassword'])->name('updatePassword');
    // user CRUD operation
    Route::resource('users', UserController::class);
    // patient wise unpaid bills
    Route::get('patients/{patient_id}/unpaidBills/{bill_type}', [PatientController::class, 'unpaid_bills']);
    // patient CRUD operation
    Route::resource('patients', PatientController::class);
    // patient admission CRUD operation
    Route::resource('patientAdmissions', PatientAdmissionController::class);
    // patient admission facesheet data
    Route::get('patientAdmissions/{id}/facesheet', [PatientAdmissionController::class, 'facesheet']);

    /* ******************** End User Management *********************** */



    /* #################### Start Requisition Management #################### */
    /* Hospital Requisition */
    Route::resource('requisitions', RequisitionController::class);
    Route::post('requisition-upload-image/{requisition}', [RequisitionController::class, 'uploadImage'])->name('requisition_upload_image');

    Route::post('requisition-item-add', [RequisitionController::class, 'addItem'])->name('requisition_item_add');
    Route::put('requisition-item-update/{id}', [RequisitionController::class, 'updateItem'])->name('requisition_item_update');
    Route::delete('requisition-item-delete/{id}', [RequisitionController::class, 'deleteItem'])->name('requisition_item_delete');
    Route::put('requisition-item-cancel/{id}', [RequisitionController::class, 'cancelItem'])->name('requisition_item_cancel');
    Route::get('requisition-new', [RequisitionController::class, 'new'])->name('requisition_new');

    Route::get('dept-requisitions', [RequisitionController::class, 'deptIndex'])->name('dept_requisitions');
    Route::get('dept-requisition-create', [RequisitionController::class, 'deptCreate'])->name('dept_requisition_create');
    Route::get('dept-inventories', [InventoryController::class, 'deptIndex'])->name('dept_inventories');

    Route::resource('inventories', InventoryController::class);
    Route::resource('consumptions', DepartmentConsumptionController::class);
    Route::resource('medicine-consumption', MedicineConsumptionController::class);
    Route::post('searchMedicine', [MedicineConsumptionController::class, 'searchMedicine'])->name('search-medicine');


    /* ******************** End Requisition Management ******************** */



    /* #################### Start Billing Management #################### */
    // payment receipt data
    Route::get('payments/{payment_id}/receipt', [PaymentHistoryController::class, 'receipt'])->name('money-receipt');
    // payment CRUD operation
    Route::resource('payments', PaymentHistoryController::class);

    // get opd billing service lists
    Route::get('opdBillings/getServices/{item_type}', [OpdBillingController::class, 'getServices']);
    // remove item from OPD billing
    Route::post('opdBillings/removeItem/{item_id}', [OpdBillingController::class, 'removeItem']);
    // add item to OPD billing
    Route::post('opdBillings/addItem', [OpdBillingController::class, 'addItem']);
    // update item to OPD billing
    Route::post('opdBillings/updateItem', [OpdBillingController::class, 'updateItem']);
    // OPD billing receipt data
    Route::get('opdBillings/{bill_id}/receipt', [OpdBillingController::class, 'receipt'])->name('opd-receipt');
    // OPD billing cart item list
    Route::get('opdBillings/{patient_id}/billItems', [OpdBillingController::class, 'billItems']);
    // add refund to OPD billing
    Route::get('opdBillings/{bill_id}/add-refund', [OpdBillingController::class, 'add_refund'])->name('opd_add_refund');
    // approve refund frontend to OPD billing
    Route::get('opdBillings/{bill_id}/approve-refund', [OpdBillingController::class, 'approve_refund'])->name('opd_approve_refund');
    // approve refund to OPD billing
    Route::post('opdBillings/{bill_id}/approve', [OpdBillingController::class, 'approve'])->name('opd_approve');
    // OPD billing CRUD operation
    Route::resource('opdBillings', OpdBillingController::class);
    // get IPD billing service list
    Route::get('ipdBillings/getServices/{item_type}', [IpdBillingController::class, 'getServices']);
    // remove item from IPD billing
    Route::post('ipdBillings/removeItem/{item_id}', [IpdBillingController::class, 'removeItem']);
    // get IPD billing cart item list
    Route::get('ipdBillings/{patient_admission_id}/billItems', [IpdBillingController::class, 'billItems']);
    // add item to IPD billing
    Route::post('ipdBillings/addItem', [IpdBillingController::class, 'addItem']);
    // update item to IPD billing
    Route::post('ipdBillings/updateItem', [IpdBillingController::class, 'updateItem']);
    // IPD billing receipt data
    Route::get('ipdBillings/{bill_id}/receipt', [IpdBillingController::class, 'receipt'])->name('ipd-receipt');
    // add refund to IPD billing
    Route::get('ipdBillings/{bill_id}/add-refund', [IpdBillingController::class, 'add_refund'])->name('add_refund');
    // approve refund UI for IPD billing
    Route::get('ipdBillings/{bill_id}/approve-refund', [IpdBillingController::class, 'approve_refund'])->name('approve_refund');
    // approve refund to IPD billing
    Route::post('ipdBillings/{bill_id}/approve', [IpdBillingController::class, 'approve'])->name('approve');
    // IPD billing CRUD operation
    Route::resource('ipdBillings', IpdBillingController::class);

    Route::get('billing/pending-refunds', [IpdBillingController::class, 'pending_refunds'])->name('pending_refunds');
    Route::get('payment/my-summery', [PaymentHistoryController::class, 'my_summery'])->name('my_summery');
    Route::get('payment/get-bill-summery', [PaymentHistoryController::class, 'get_my_summery'])->name('get_my_summery');

    // Referral setting crud operation
    Route::resource('referralSetting', ReferralSettingController::class);
    Route::get('referralStatement', [ReferralController::class, 'referralStatement'])->name('referralStatement');
    Route::get('referral/{person_id}', [ReferralController::class, 'referralHistory'])->name('referralHistory');
    Route::get('referral/{person_id}/referralSummery', [ReferralController::class, 'referralSummery'])->name('referralSummery');
    Route::get('referral/{person_id}/paymentHistory', [ReferralController::class, 'paymentHistory'])->name('paymentHistory');
    Route::post('referral/{person_id}/makePayment', [ReferralController::class, 'makePayment'])->name('makePayment');

    /* ******************** End Billing Management ******************** */



    /* #################### Start Common Settings #################### */
    Route::get('hospitals/list', [HospitalController::class, 'list']);
    Route::resource('hospitals', HospitalController::class);
    Route::get('hospitals/{hospital_id}/departments', [HospitalController::class, 'departments']);
    Route::get('corporateClients/{client_id}/checkDiscount/{bill_type}/{item_type}/{item_id}', [CorporateClientController::class, 'checkDiscount']);
    Route::resource('corporateClients', CorporateClientController::class);
    Route::get('discounts/service_packages_list/{category}', [CorporateClientDiscountController::class, 'service_packages_list']);
    Route::resource('corporateClients.discounts', CorporateClientDiscountController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('opdServices', OpdServiceController::class);
    Route::resource('ipdServices', IpdServiceController::class);
    Route::resource('opdServicePackages', OpdServicePackageController::class);
    Route::resource('ipdServicePackages', IpdServicePackageController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('designations', DesignationController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('doctor-specialities', DoctorsSpecialityController::class);
    Route::resource('stores', StoreController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('product-brands', ProductBrandController::class);
    Route::resource('product-categories', ProductCategoryController::class);
    Route::resource('pathologies', PathologyController::class);
    Route::resource('product-models', ProductModelController::class);
    Route::resource('products', ProductController::class);

    /* #################### End Common Settings #################### */
});
