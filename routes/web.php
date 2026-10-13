<?php
namespace App\Http\Controllers;
use App\Http\Controllers\ItemUnits;
use App\Http\Controllers\RegisterUser;
use App\Http\Controllers\SaleTypeController;
use App\Http\Controllers\StockOpeningController;
use App\Http\Controllers\ProductGroupController;

use App\Http\Controllers\StockReportControllers;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LotInReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OpeningStockController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\LedgerDetailController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\StockFinsihedProduct;
use App\Http\Controllers\SaleReturnInvoiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\ProductionStockReport;
use App\Http\Controllers\ErpParamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Auth\LoginController;
            use App\Http\Controllers\ProductMasterController;

use App\Http\Controllers\BankReciptController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\BankPaymentController;
use App\Http\Controllers\OfficeCashController;
use App\Http\Controllers\GateExController;
use App\Http\Controllers\account\BillController;
use App\Http\Controllers\account\LoanController;
use App\Http\Controllers\GluePurchaseController;
use App\Http\Controllers\GlueReturnController;
use App\Http\Controllers\InkPurchaseController;
use App\Http\Controllers\InkReturnController;
use App\Http\Controllers\account\GroupController;
use App\Http\Controllers\account\PartyController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PlatePurchaseController;
use App\Http\Controllers\PlateReturnController;
use App\Http\Controllers\account\Level1Controller;
use App\Http\Controllers\account\Level2Controller;
use App\Http\Controllers\account\Level3Controller;
use App\Http\Controllers\JournalVoucherController;
use App\Http\Controllers\OpenBalController;
use App\Http\Controllers\PaymentInvoiceController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\DeliveryChallanController;
use App\Http\Controllers\ConfectioneryController;
use App\Http\Controllers\RecieveableableController;
use App\Http\Controllers\category\CategoryController;
use App\Http\Controllers\LeminationPurchaseController;
use App\Http\Controllers\LaminationReturnController;
use App\Http\Controllers\CorrugationPurchaseController;
use App\Http\Controllers\CorrugationReturnController;
use App\Http\Controllers\ShipperPurchasesController;
use App\Http\Controllers\ShipperReturnController;
use App\Http\Controllers\account\AccountMasterController;
use App\Http\Controllers\WastageSaleController;
use App\Http\Controllers\WastageController;
use App\Http\Controllers\SaleInvoiceController;
use App\Http\Controllers\ConfectBillingController;
use App\Http\Controllers\GatePassInController;
use App\Http\Controllers\GatePassOutController;
use App\Http\Controllers\RegistrationFormController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StorageLinkController;
use App\Http\Controllers\PhpIniController;
use App\Http\Controllers\PhpInfoController;
use App\Http\Controllers\ProductLogController;
use App\Http\Controllers\ChequeReceiptsController;
use App\Http\Controllers\DatabaseTestController;
use App\Http\Controllers\CreateAccountController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SReportsController;
use App\Http\Controllers\DailyStatementController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\JobSheetController;
use App\Http\Controllers\CustomController;
use App\Http\Controllers\DepartmentSectionController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\ExtraTimeController;
use App\Http\Controllers\DyeSectionController;
use App\Http\Controllers\PasteSectionController;
use App\Http\Controllers\ProcessSectionController;
use App\Http\Controllers\AttendenceFormController;
use App\Http\Controllers\DyePurchaseController;
use App\Http\Controllers\DyeReturnController;
use App\Http\Controllers\GeneralJobSheetController;
use App\Http\Controllers\GeneralDeliveryChallanController;
use App\Http\Controllers\GeneralBillingController;
use App\Http\Controllers\WageBoxboardController;
use App\Http\Controllers\SalaryCalculatorController;
use App\Http\Controllers\BankCashReportController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\Customer;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\PurchaseReturnInvoiceController;
use App\Http\Controllers\SaleInvoices;
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\SaleOrderDcController;
use App\Http\Controllers\CashBookController;
use App\Http\Controllers\HistoricalRateImportController;
use App\Http\Controllers\TrialBalanceController;
use App\Http\Controllers\CompanyRegController;
use App\Http\Controllers\Consumption2Controller;
use App\Http\Controllers\ConsumptionController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\StockAdjController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\ReportStockController;
use App\Http\Controllers\UserManageController;
use App\Http\Controllers\UserPermissionManage;
use App\Http\Controllers\PrintInvoice;
use App\Http\Controllers\ItemWiseController;
use App\Http\Controllers\TypeWiseController;
use App\Http\Controllers\CustomerWiseReportController;
use App\Http\Controllers\CashReciptReport;
use App\Http\Controllers\ItemMoldController;
use App\Http\Controllers\ReportPurchaser;
use App\Http\Controllers\ItemSelectedController;
use App\Http\Controllers\RawSaleInvoiceController;
use App\Http\Controllers\RawSaleReturnInvoiceController;
use App\Http\Controllers\CustomerCategoryController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('')->group(
    function () {
        Route::get('/config-clear', function () {
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            return "✅ Config cleared and cached!";
        });

        // Route to run migrations via web (admin only, secure as needed)
        Route::get('/migrate', function () {
            Artisan::call('migrate', ['--force' => true]);
            return "✅ Migration completed!";
        })->middleware(['auth', 'admin']);

        Route::get('/backup', [BackupController::class, 'runBackup'])->name('admin.backup');

        Route::get('/', function () {
            if (\Illuminate\Support\Facades\Auth::check()) {
                return redirect()->route('dashboard.admin');
            }
            return \Inertia\Inertia::render('Auth/Login');
        })->name('home');

        Route::get('/react', function () {
            return \Inertia\Inertia::render('Welcome', [
                'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        })->name('react');

        Route::get('/welcome', function () {
            return \Inertia\Inertia::render('Welcome', [
                'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        })->name('welcome');

        Route::get('/create-storage-link', [StorageLinkController::class, 'createLink']);

        Route::middleware(['auth', 'admin'])->group(function () {
            Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard.admin');
            Route::get('/create_account', [CreateAccountController::class, 'index'])->name('create_account.list');
            Route::get('/create_account/reports', [CreateAccountController::class, 'reports'])->name('create_account.reports');
            Route::post('/create_account', [CreateAccountController::class, 'store'])->name('create_account.store');
            Route::get('/create_account/delete/{id}', [CreateAccountController::class, 'delete'])->name('create_account.delete');
            Route::get('/create_account/edit/{id}', [CreateAccountController::class, 'edit'])->name('create_account.edit');
            Route::put('/create_account/update/{id}', [CreateAccountController::class, 'update'])->name('create_account.update');
        });



        Route::middleware('auth')->group(function () {


Route::prefix('product-master')->name('product.master.')->group(function () {

    Route::get('/', [ProductMasterController::class, 'index'])->name('list');

    Route::get('/create', [ProductMasterController::class, 'create'])->name('create');

    Route::post('/', [ProductMasterController::class, 'store'])->name('store');

    Route::get('/{id}/edit', [ProductMasterController::class, 'edit'])->name('edit');

    Route::put('/{id}', [ProductMasterController::class, 'update'])->name('update');

    Route::delete('/{id}', [ProductMasterController::class, 'destroy'])->name('destroy');
});
           
Route::get('/product-groups', [ProductGroupController::class, 'index'])
        ->name('product-groups.index');
    
    // Show create form
    Route::get('/product-groups/create', [ProductGroupController::class, 'create'])
        ->name('product-groups.create');
    
    // Store new product group
    Route::post('/product-groups', [ProductGroupController::class, 'store'])
        ->name('product-groups.store');
    
    // Show edit form
    Route::get('/product-groups/{id}/edit', [ProductGroupController::class, 'edit'])
        ->name('product-groups.edit');
    
    // Update product group
    Route::put('/product-groups/{id}', [ProductGroupController::class, 'update'])
        ->name('product-groups.update');
    
    // Delete product group
    Route::delete('/product-groups/{id}', [ProductGroupController::class, 'destroy'])
        ->name('product-groups.destroy');

            Route::get('/trialbalance', [TrialBalanceController::class, 'index'])->name('trialbalance.list');
            Route::get('/trial', [TrialBalanceController::class, 'summary'])->name('trialbalance.summarized');
            Route::get('/trialreport', [TrialBalanceController::class, 'main'])->name('trialbalance.index');
            Route::resource('/stock-adj', StockAdjController::class)->names('stock-adj');
            Route::get('/currentStock', [StockReportControllers::class, 'index'])->name('current');
            Route::get('/finishgood', [StockFinsihedProduct::class, 'index'])->name('currentp');
            Route::resource('/production', ProductionController::class)->names('production');
            Route::resource('/register_company', CompanyRegController::class)->names('company_reg');
            Route::post('company_reg/{company}/assign-users', [CompanyRegController::class, 'assignUsers'])->name('company_reg.assignUsers');

            Route::get('/consumption/print/{id}', [ConsumptionController::class, 'print'])->name('consumption.print');
Route::get('/consumption/material-rate/{itemId}', [ConsumptionController::class, 'getMaterialRate'])->name('consumption.material-rate');

            Route::get('/consumption2/print/{id}', [Consumption2Controller::class, 'print'])->name('consumption2.print');
            Route::get('/consumption2/material-rate/{itemId}', [Consumption2Controller::class, 'getMaterialRate'])->name('consumption2.material-rate');

            Route::get('/salary_calc', [SalaryCalculatorController::class, 'index'])->name('salary_calc.list');
            Route::post('/salary_calc/get-data', [SalaryCalculatorController::class, 'getSalaryData'])->name('salary_calc.get_data');

            Route::get('/purchase-invoices/{v_no}/print', [PurchaseInvoiceController::class, 'print'])
                ->name('purchase_invoices.print');
            Route::get('/purchase-return-invoices/{v_no}/print', [PurchaseReturnInvoiceController::class, 'print'])
                ->name('purchase_return_invoices.print');


            Route::get('/sale-invoices/{v_no}/print', [SaleInvoices::class, 'print'])
                ->name('sale_invoices.print');
            Route::get('/production/{id}/print', [ProductionController::class, 'print'])->name('production.print');

            Route::get('/sale-return-invoices/{v_no}/print', [SaleReturnInvoiceController::class, 'print'])
                ->name('sale_return_invoices.print');

            // Raw Sale Invoices
            Route::prefix('raw-sale-invoices')->name('raw_sale_invoices.')->group(function () {
                Route::get('/', [RawSaleInvoiceController::class, 'index'])->name('index');
                Route::get('/create', [RawSaleInvoiceController::class, 'create'])->name('create');
                Route::post('/', [RawSaleInvoiceController::class, 'store'])->name('store');
                Route::get('/{v_no}/edit', [RawSaleInvoiceController::class, 'edit'])->name('edit');
                Route::put('/{v_no}', [RawSaleInvoiceController::class, 'update'])->name('update');
                Route::delete('/{v_no}', [RawSaleInvoiceController::class, 'destroy'])->name('destroy');
                Route::get('/{v_no}/print', [RawSaleInvoiceController::class, 'print'])->name('print');
            });

            // Raw Return Invoices
            Route::prefix('raw-sale-return-invoices')->name('raw_sale_return_invoices.')->group(function () {
                Route::get('/', [RawSaleReturnInvoiceController::class, 'index'])->name('index');
                Route::get('/create', [RawSaleReturnInvoiceController::class, 'create'])->name('create');
                Route::post('/', [RawSaleReturnInvoiceController::class, 'store'])->name('store');
                Route::get('/{v_no}/edit', [RawSaleReturnInvoiceController::class, 'edit'])->name('edit');
                Route::put('/{v_no}', [RawSaleReturnInvoiceController::class, 'update'])->name('update');
                Route::delete('/{v_no}', [RawSaleReturnInvoiceController::class, 'destroy'])->name('destroy');
                Route::get('/{v_no}/print', [RawSaleReturnInvoiceController::class, 'print'])->name('print');
            });
            
            Route::resource('/customer-categories', CustomerCategoryController::class);

            Route::get('/attendence_form', [AttendenceFormController::class, 'index'])->name('attendence_form.list');
            Route::post('/attendence_form', [AttendenceFormController::class, 'store'])->name('attendence_form.store');
            Route::get('/attendence_form/reports', [AttendenceFormController::class, 'reports'])->name('attendence_form.reports');
            Route::delete('/attendence_form/{id}', [AttendenceFormController::class, 'destroy'])->name('attendence_form.destroy');
            Route::get('/attendence_form/{id}/edit', [AttendenceFormController::class, 'edit'])->name('attendence_form.edit');
            Route::put('/attendence_form/{id}', [AttendenceFormController::class, 'update'])->name('attendence_form.update');

            Route::get('/check-attendance-status', [AttendenceFormController::class, 'checkAttendanceStatus'])->name('check.attendance.status');


            Route::get('/get-boxboard-details/{item_id}', [JobSheetController::class, 'getBoxboardDetails']);
            Route::get('/get-ink-details/{item_id}', [JobSheetController::class, 'getinkDetails']);

            Route::get('/get-lamination-details', [JobSheetController::class, 'getLaminationDetails']);
            Route::get('/get-glue-details/{item_id}', [JobSheetController::class, 'getglueDetails']);
            Route::get('/get-shipper-details/{item_id}', [JobSheetController::class, 'getshipperDetails']);
            Route::get('/job_sheet', [JobSheetController::class, 'index'])->name('job.index');
            Route::post('/job_sheet/store', [JobSheetController::class, 'store'])->name('job.store');
            Route::get('/job_sheet/report', [JobSheetController::class, 'report'])->name('job.report');
            Route::delete('/job-details', [JobSheetController::class, 'destroy'])->name('job-details.destroy');
            Route::get('/job-details/{v_no}/edit', [JobSheetController::class, 'edit'])->name('job-details.edit');
            Route::put('/job-details/{v_no}', [JobSheetController::class, 'update'])->name('job-details.update');
            Route::get('/get-product-details', [JobSheetController::class, 'getProductDetails'])->name('get.product.details');

            Route::get('/get-products/{customerId}', [JobSheetController::class, 'getProducts']);
            Route::get('/fetch-custom-rate', [JobSheetController::class, 'fetchRate'])->name('fetch.custom.rate');
            Route::get('/fetch-shipper-stock', [JobSheetController::class, 'fetchShipperStock'])->name('fetch.shipper.stock');
            Route::get('/fetch-corrugation-stock', [JobSheetController::class, 'fetchCorrugationStock'])->name('fetch.corrugation.stock');


            Route::get('/daily_statement/reports', [DailyStatementController::class, 'reports'])->name('daily_statement.reports');

            Route::get('/expense/reports', [ExpenseController::class, 'reports'])->name('expense.reports');




            Route::get('/general/job/sheet', [GeneralJobSheetController::class, 'index'])->name('general_job_sheet.list');
            Route::post('/general-job-sheet', [GeneralJobSheetController::class, 'store'])->name('general-job-sheet.store');
            Route::get('/general-job-sheet/report', [GeneralJobSheetController::class, 'report'])->name('general_job_sheet.report');
            Route::delete('/general-job-sheet/{id}', [GeneralJobSheetController::class, 'destroy'])->name('general_job_sheet.destroy');
            Route::get('/get-purchase-items', [GeneralJobSheetController::class, 'getPurchaseItems']);
            Route::get('/get-purchase-item-details', [GeneralJobSheetController::class, 'getPurchaseItemDetails']);
            Route::get('/general-job-sheet/{id}/edit', [GeneralJobSheetController::class, 'edit'])->name('general_job_sheet.edit');
            Route::put('/general-job-sheet/{id}', [GeneralJobSheetController::class, 'update'])->name('general_job_sheet.update');


            Route::get('/general/delivery/challan', [GeneralDeliveryChallanController::class, 'index'])->name('general_delivery_challan.list');
            Route::get('/get-general-job-sheet-data', [GeneralDeliveryChallanController::class, 'getGeneralJobSheetData']);
            Route::post('/general/delivery/challan/store', [GeneralDeliveryChallanController::class, 'store'])->name('general_delivery_challan.store');
            Route::get('/general/delivery/challan/report', [GeneralDeliveryChallanController::class, 'report'])->name('general_delivery_challan.report');
            Route::delete('/general-delivery-challan/{id}', [GeneralDeliveryChallanController::class, 'destroy'])->name('general_delivery_challan.destroy');
            Route::get('/general-delivery-challan/{id}/edit', [GeneralDeliveryChallanController::class, 'edit'])->name('general_delivery_challan.edit');
            Route::put('/general-delivery-challan//{id}', [GeneralDeliveryChallanController::class, 'update'])->name('general_delivery_challan.update');


            Route::get('/general/billing', [GeneralBillingController::class, 'index'])->name('general_billing.list');
            Route::post('/general/billing/store', [GeneralBillingController::class, 'store'])->name('general_billing.store');
            Route::get('/general/billing/report', [GeneralBillingController::class, 'report'])->name('general_billing.report');
            Route::delete('/general/billing/{id}', [GeneralBillingController::class, 'destroy'])->name('general_billing.destroy');
            Route::get('/get-voucher-numbers/{partyId}', [GeneralBillingController::class, 'getVoucherNumbers'])->name('get.voucher.numbers');
            Route::get('/get-voucher-details/{voucherNo}', [GeneralBillingController::class, 'getVoucherDetails']);
            Route::get('/check-existing-billings', [GeneralBillingController::class, 'checkExistingBillings']);



            Route::get('/boxboard/wage', [WageBoxboardController::class, 'index'])->name('boxboard_wage.list');
            Route::get('/boxboard/wage/report', [WageBoxboardController::class, 'report'])->name('boxboard_wage.report');
            Route::get('/boxboard/wage/vouchers/{employee_id}', [WageBoxboardController::class, 'getVouchersByEmployee'])->name('boxboard_wage.vouchers');
            Route::get('/boxboard/wage/details/{employee_id}/{v_no}', [WageBoxboardController::class, 'getVoucherDetails'])->name('boxboard_wage.details');
            Route::post('/boxboard/wage/store', [WageBoxboardController::class, 'store'])->name('boxboard_wage.store');

            Route::delete('/boxboard/wage/store/{id}', [WageBoxboardController::class, 'destroy'])->name('boxboard_wage.destroy');

            // Route::get('/reports/stock', [ReportStockController::class, 'reports'])->name('report.stock');
            // Route::get('/report_stock', [App\Http\Controllers\ReportStockController::class, 'index'])->name('report_stock.index');
            // Route::get('/report_stock/create', [App\Http\Controllers\ReportStockController::class, 'create'])->name('report_stock.create');
            // Route::post('/report_stock', [App\Http\Controllers\ReportStockController::class, 'store'])->name('report_stock.store');
            // Route::get('/report_stock/{id}/edit', [App\Http\Controllers\ReportStockController::class, 'edit'])->name('report_stock.edit');
            // Route::post('/report_stock/{id}/edit', [App\Http\Controllers\ReportStockController::class, 'edit'])->name('report_stock.edit.post');
            // Route::put('/report_stock/{id}', [App\Http\Controllers\ReportStockController::class, 'update'])->name('report_stock.update');
            // Route::delete('/report_stock/{id}', [App\Http\Controllers\ReportStockController::class, 'destroy'])->name('report_stock.destroy');


            Route::get('/purchase/reports', [ReportsController::class, 'reports'])->name('purchase.reports');


            Route::get('/sale/reports', [SReportsController::class, 'reports'])->name('sale.reports');
            Route::get('/bank_cash/reports', [BankCashReportController::class, 'reports'])->name('bank_cash.reports');


            Route::get('/get-item-details/{id}', [PaymentInvoiceController::class, 'getItemDetails'])->name('getItemDetails');
            Route::post('/update-status/{id}', [CashController::class, 'updateStatus'])->name('cash.updateStatus');

            Route::get('/user/dashboard', [DashboardController::class, 'user_index'])->name('dashboard.user');
            Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');

            Route::get('/department', [DepartmentController::class, 'index'])->name('department.list');
            Route::get('/department/create', [DepartmentController::class, 'create'])->name('department.create');
            Route::post('/department', [DepartmentController::class, 'store'])->name('department.store');
            Route::get('/department/{id}/edit', [DepartmentController::class, 'edit'])->name('department.edit');
            Route::post('/department/{id}', [DepartmentController::class, 'update'])->name('department.update');
            Route::delete('/department/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');

            Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.list');

            Route::get('/employees', [EmployeesController::class, 'index'])->name('employees.list');
            Route::post('/employees', [EmployeesController::class, 'store'])->name('employees.store');
            Route::get('/employees/reports', [EmployeesController::class, 'reports'])->name('employees.reports');
            Route::delete('/employees/{id}', [EmployeesController::class, 'destroy'])->name('employees.destroy');
            Route::get('/employees/{id}/edit', [EmployeesController::class, 'edit'])->name('employees.edit');
            Route::put('/employees/{id}', [EmployeesController::class, 'update'])->name('employees.update');
            Route::get('/extra-times/{id}', [EmployeesController::class, 'getRate']);

            Route::get('/employee_type', [EmployeeTypeController::class, 'index'])->name('employee_type.list');
            Route::post('/employee_type', [EmployeeTypeController::class, 'store'])->name('employee_type.store');
            Route::get('/get-employee-details/{id}', [EmployeeTypeController::class, 'getEmployeeDetails']);


            Route::get('/employee_type/reports', [EmployeeTypeController::class, 'reports'])->name('employee_type.reports');
            Route::get('/employee_type/{id}/edit', [EmployeeTypeController::class, 'edit'])->name('employee_type.edit');
            Route::put('/employee_type/{id}', [EmployeeTypeController::class, 'update'])->name('employee_type.update');
            Route::delete('/employee_type/{id}', [EmployeeTypeController::class, 'destroy'])->name('employee_type.destroy');

            Route::get('/category', [CategoryController::class, 'index'])->name('category.list');
            Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
            Route::get('/category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
            Route::post('/category/{id}', [CategoryController::class, 'update'])->name('category.update');
            Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
            Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');

            Route::get('/bank', [BankController::class, 'index'])->name('bank.list');
            Route::post('/bank', [BankController::class, 'store'])->name('bank.store');

            Route::get('/erp_param', [ErpParamController::class, 'index'])->name('erp_param.list');
            Route::get('/erp_param/create', [ErpParamController::class, 'create'])->name('erp_param.create');
            Route::post('/erp_param', [ErpParamController::class, 'store'])->name('erp_param.store');
            Route::get('/erp_param/{id}/edit', [ErpParamController::class, 'edit'])->name('erp_param.edit');
            Route::put('/erp_param/{id}', [ErpParamController::class, 'update'])->name('erp_param.update');
            Route::delete('/erp_param/{id}', [ErpParamController::class, 'destroy'])->name('erp_param.destroy');

            Route::get('/cash', [CashController::class, 'index'])->name('cash.list');
            Route::get('/cash/reports', [CashController::class, 'reports'])->name('cash.reports');
            Route::post('/cash', [CashController::class, 'store'])->name('cash.store');
            Route::put('/cash/{v_no}/update', [CashController::class, 'update'])->name('cash.update'); 
            Route::get('/cash/{v_no}/edit', [CashController::class, 'edit'])->name('cash.edit');
            Route::get('/cash/{id}', [CashController::class, 'destroy'])->name('cash.destroy');
            Route::delete('/cash-delete/{id}', [CashController::class, 'delete'])->name('cash.delete');
            Route::get('/cash/create', [CashController::class, 'create'])->name('cash.create');


            Route::get('/payment', [PaymentController::class, 'index'])->name('payment.list');
            Route::get('/paymentreports', [PaymentController::class, 'reports'])->name('payment.reports');
            Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
            Route::get('invoicing/{id}/{type}', [PrintInvoice::class, 'print'])->name('printInvoice');
            Route::get('/payment/{v_no}/edit', [PaymentController::class, 'edit'])->name('payment.edit');
            Route::put('/payment/{v_no}/update', [PaymentController::class, 'update'])->name('payment.update');
            Route::get('/payment/{id}', [PaymentController::class, 'destroy'])->name('payment.destroy');
            Route::delete('/payment-delete/{id}', [PaymentController::class, 'delete'])->name('payment.delete');
            Route::get('/payment/create', [PaymentController::class, 'create'])->name('payment.create');


            Route::get('/bank_payment', [BankPaymentController::class, 'index'])->name('bank_payment.list');
            Route::get('/bank_payment/create', [BankPaymentController::class, 'create'])->name('bank_payment.create');
            Route::post('/bank_payment', [BankPaymentController::class, 'store'])->name('bank_payment.store');
            Route::get('/bank_payment/reports', [BankPaymentController::class, 'reports'])->name('bank_payment.reports');
            Route::get('/bank_payment/{v_no}/edit', [BankPaymentController::class, 'edit'])->name('bank_payment.edit');
            Route::put('/bank_payment/{v_no}/update', [BankPaymentController::class, 'update'])->name('bank_payment.update');
            Route::get('/bank_payment/{id}', [BankPaymentController::class, 'destroy'])->name('bank_payment.destroy');
            Route::delete('/bank_payment-delete/{id}', [BankPaymentController::class, 'delete'])->name('bank_payment.delete');

            Route::get('/gate_ex', [GateExController::class, 'index'])->name('gate_ex.list');
            Route::post('/gate_ex', [GateExController::class, 'store'])->name('gate_ex.store');
            Route::get('/gate_ex/reports', [GateExController::class, 'reports'])->name('gate_ex.reports');
            Route::get('/gate_ex/{v_no}/edit', [GateExController::class, 'edit'])->name('gate_ex.edit');
            Route::put('/gate_ex/{v_no}/update', [GateExController::class, 'update'])->name('gate_ex.update');
            Route::get('/gate_ex/{id}', [GateExController::class, 'destroy'])->name('gate_ex.destroy');
            Route::delete('/gate_ex-delete/{id}', [GateExController::class, 'delete'])->name('gate_ex.delete');


            Route::get('/office_cash', [OfficeCashController::class, 'index'])->name('office_cash.list');
            Route::post('/office_cash', [OfficeCashController::class, 'store'])->name('office_cash.store');
            Route::get('/office_cash/reports', [OfficeCashController::class, 'reports'])->name('office_cash.reports');
            Route::get('/office_cash/{v_no}/edit', [OfficeCashController::class, 'edit'])->name('office_cash.edit');
            Route::put('/office_cash/{v_no}/update', [OfficeCashController::class, 'update'])->name('office_cash.update');
            Route::get('/office_cash/{id}', [OfficeCashController::class, 'destroy'])->name('office_cash.destroy');
            Route::delete('/office_cash-delete/{id}', [OfficeCashController::class, 'delete'])->name('office_cash.delete');

            Route::get('/bank_recipt', [BankReciptController::class, 'index'])->name('bank_recipt.list');
            Route::post('/bank_recipt', [BankReciptController::class, 'store'])->name('bank_recipt.store');
            Route::get('/bank_recipt/reports', [BankReciptController::class, 'reports'])->name('bank_recipt.reports');
            Route::get('/bank_recipt/{id}/edit', [BankReciptController::class, 'edit'])->name('bank_recipt.edit');
            Route::put('/bank_recipt/{v_no}/update', [BankReciptController::class, 'update'])->name('bank_recipt.update');
            Route::get('/bank_recipt/{id}', [BankReciptController::class, 'destroy'])->name('bank_recipt.destroy');
            Route::delete('/bank_recipt-delete/{id}', [BankReciptController::class, 'delete'])->name('bank_recipt.delete');

            Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger.list');
            Route::get('/ledger_detail', [LedgerDetailController::class, 'index'])->name('ledger_detail.list');

            Route::get('/payables', [PayableController::class, 'index'])->name('payables.list');

            Route::get('/recieveables', [RecieveableableController::class, 'index'])->name('recieveables.list');
            Route::get('/cashbook', [CashBookController::class, 'index'])->name('cashbooks.list');

            Route::get('/journal_voucher', [JournalVoucherController::class, 'index'])->name('journal_voucher.list');
            Route::post('/journal_voucher', [JournalVoucherController::class, 'store'])->name('journal_voucher.store');
            Route::get('/journal_voucher/reports', [JournalVoucherController::class, 'reports'])->name('journal_voucher.reports');
            Route::get('/journal_voucher/{v_no}/edit', [JournalVoucherController::class, 'edit'])->name('journal_voucher.edit');
            Route::get('/journal_voucher/delete/{id}', [JournalVoucherController::class, 'delete'])->name('journal_voucher.delete');
            Route::put('/journal_voucher/{v_no}/update', [JournalVoucherController::class, 'update'])->name('journal_voucher.update');
            Route::delete('/journal_voucher/{id}', [JournalVoucherController::class, 'destroy'])->name('journal_voucher.destroy');

            Route::get('/open_bal', [OpenBalController::class, 'index'])->name('open_bal.list');
            Route::post('/open_bal', [OpenBalController::class, 'store'])->name('open_bal.store');
            Route::get('/open_bal/reports', [OpenBalController::class, 'reports'])->name('open_bal.reports');
            Route::get('/open_bal/edit', [OpenBalController::class, 'edit'])->name('open_bal.edit');
            Route::get('/open_bal/delete', [OpenBalController::class, 'delete'])->name('open_bal.delete');
            Route::put('/open_bal/update', [OpenBalController::class, 'update'])->name('open_bal.update');
            Route::delete('/open_bal', [OpenBalController::class, 'destroy'])->name('open_bal.destroy');
            Route::get('/open_bal/get-balance/{accountId}', [OpenBalController::class, 'getBalance'])->name('open_bal.getBalance');


            Route::get('/employee/create', [EmployeeController::class, 'create'])->name('employee.create');
            Route::get('/employee/create1', [EmployeeController::class, 'create1'])->name('employee.create1');
            Route::post('/employee', [EmployeeController::class, 'store'])->name('employee.store');
            Route::get('/employee/{id}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
            Route::put('/employee/{id}', [EmployeeController::class, 'update'])->name('employee.update');
            Route::delete('/employee/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');

            Route::post('/account/level1', [Level1Controller::class, 'store'])->name('level1.store');
            Route::get('/account/level1', [Level1Controller::class, 'index'])->name('level1.list');
            Route::get('/account/level1/create', [Level1Controller::class, 'create'])->name('level1.create');
            Route::get('/account/level1/{id}/edit', [Level1Controller::class, 'edit'])->name('level1.edit');
            Route::post('/account/level1/{id}', [Level1Controller::class, 'update'])->name('level1.update');
            Route::delete('/account/level1/{id}', [Level1Controller::class, 'destroy'])->name('level1.destroy');

            Route::post('/account/level2', [Level2Controller::class, 'store'])->name('level2.store');
            Route::get('/account/level2', [Level2Controller::class, 'index'])->name('level2.list');
            Route::get('/account/level2/create', [Level2Controller::class, 'create'])->name('level2.create');
            Route::get('/account/level2/{id}/edit', [Level2Controller::class, 'edit'])->name('level2.edit');
            Route::post('/account/level2/{id}', [Level2Controller::class, 'update'])->name('level2.update');
            Route::delete('/account/level2/{id}', [Level2Controller::class, 'destroy'])->name('level2.destroy');

            Route::post('/account/level3', [Level3Controller::class, 'store'])->name('level3.store');
            Route::get('/account/level3', [Level3Controller::class, 'index'])->name('level3.list');
            Route::get('/account/level3/create', [Level3Controller::class, 'create'])->name('level3.create');

            Route::post('/account/group', [GroupController::class, 'store'])->name('group.store');
            Route::get('/account/group', [GroupController::class, 'index'])->name('group.list');
            Route::get('/account/group/create', [GroupController::class, 'create'])->name('group.create');

            Route::post('/account/a_master', [AccountMasterController::class, 'store'])->name('amaster.store');
            Route::get('/account/a_master', [AccountMasterController::class, 'index'])->name('amaster.list');
            Route::get('/account/reports', [AccountMasterController::class, 'reports'])->name('account.reports');
            Route::get('/account/a_master/create', [AccountMasterController::class, 'create'])->name('amaster.create');
            Route::get('/account/a_master/{id}/edit', [AccountMasterController::class, 'edit'])->name('amaster.edit');
            Route::post('/account/a_master/{id}', [AccountMasterController::class, 'update'])->name('amaster.update');
            Route::delete('/account/a_master/{id}', [AccountMasterController::class, 'destroy'])->name('amaster.destroy');

            Route::post('/amount/party', [PartyController::class, 'store'])->name('party.store');
            Route::get('/account/party', [PartyController::class, 'index'])->name('party.list');
            Route::get('/account/party/create', [PartyController::class, 'create'])->name('party.create');
            Route::get('/account/bill', [BillController::class, 'index'])->name('bill.list');
            Route::post('/account/bill', [BillController::class, 'store'])->name('bill.store');
            Route::get('/account/bill/create', [BillController::class, 'create'])->name('bill.create');
            Route::get('/account/loan', [LoanController::class, 'index'])->name('loan.list');
            Route::post('/account/loan', [LoanController::class, 'store'])->name('loan.store');
            Route::get('/account/loan/create', [LoanController::class, 'create'])->name('loan.create');

            Route::get('/inventory/itemmaster', [InventoryController::class, 'index_itemmaster'])->name('inventory.itemmaster.list');
            Route::get('/inventory/itemmaster/{id}/edit', [InventoryController::class, 'itemmasteredit'])->name('inventory.itemmaster.edit');
            Route::post('/inventory/itemmaster/{id}', [InventoryController::class, 'itemmasterupdate'])->name('inventory.itemmaster.update');
            Route::delete('/inventory/itemmaster/{id}', [InventoryController::class, 'itemmasterdestroy'])->name('inventory.itemmaster.destroy');

            Route::get('/inventory/itemtype', [InventoryController::class, 'index_itemtype'])->name('inventory.itemtype.list');
            Route::get('/inventory/producttype', [InventoryController::class, 'index_producttype'])->name('inventory.producttype.list');
            Route::post('/inventory/itemmaster', [InventoryController::class, 'itemmaster'])->name('inventory.itemmaster');
            Route::post('/inventory/itemtype', [InventoryController::class, 'itemtype'])->name('inventory.itemtype');
            Route::get('/inventory/itemtype/{id}/edit', [InventoryController::class, 'itemtypeedit'])->name('inventory.itemtype.edit');
            Route::get('/inventory/producttype/{id}/edit', [InventoryController::class, 'producttypeedit'])->name('inventory.producttype.edit');
            Route::post('/inventory/itemtype/{id}', [InventoryController::class, 'itemtypeupdate'])->name('inventory.itemtype.update');
            Route::post('/inventory/producttype/{id}', [InventoryController::class, 'itemtypeupdate'])->name('inventory.producttype.update');
            Route::delete('/inventory/itemtype/{id}', [InventoryController::class, 'itemtypedestroy'])->name('inventory.itemtype.destroy');
            Route::delete('/inventory/producttype/{id}', [InventoryController::class, 'itemtypedestroy'])->name('inventory.producttype.destroy');
            Route::get('/inventory/create/itemmaster', [InventoryController::class, 'createitemmaster'])->name('inventory.create.itemmaster');
            Route::get('/inventory/itemLog', [InventoryController::class, 'itemlogList'])->name('inventory.item_log');

            Route::get('/stocks', [ProductionStockReport::class, 'index'])->name('stocks.index');
            Route::get('/stocks/item/{itemId}', [ProductionStockReport::class, 'getStockByItem'])->name('stocks.by-item');
            Route::post('/stocks/items', [ProductionStockReport::class, 'getStockByItems'])->name('stocks.by-items');
            Route::get('/inventory/create/itemtype', [InventoryController::class, 'createitemtype'])->name('inventory.create.itemtype');
            Route::get('/inventory/create/producttype', [InventoryController::class, 'createproducttype'])->name('inventory.create.producttype');
            Route::get('/inventory/create/mold', [InventoryController::class, 'createmold'])->name('inventory.createmold');
            Route::post('/inventory/store/mold', [InventoryController::class, 'storemold'])->name('inventory.storemold');
            Route::post('/inventory/boxboard', [InventoryController::class, 'boxboard'])->name('inventory.boxboard');
            Route::post('/inventory/lamination', [InventoryController::class, 'lamination'])->name('inventory.lamination');
            Route::post('/inventory/corrugation', [InventoryController::class, 'corrugation'])->name('inventory.corrugation');
            Route::post('/inventory/plates', [InventoryController::class, 'plates'])->name('inventory.plates');
            Route::post('/inventory/dye', [InventoryController::class, 'dye'])->name('inventory.dye');
            Route::post('/inventory/ink', [InventoryController::class, 'ink'])->name('inventory.ink');
            Route::resource('/units', ItemUnits::class)->names('units');
            Route::resource('/mold', ItemMoldController::class)->names('mold');

            Route::get('/stock_report', [StockReportController::class, 'index'])->name('stock_report.list');
            Route::post('/stock_report/store', [StockReportController::class, 'store'])->name('stock_report.store');
            Route::get('/stock_report/reports', [StockReportController::class, 'reports'])->name('stock_report.reports');


            Route::get('/delivery_challan', [DeliveryChallanController::class, 'index'])->name('delivery_challan.list');
            Route::get('/get-products/{partyId}', [DeliveryChallanController::class, 'getProducts']);

            Route::get('/delivery_challan/reports', [DeliveryChallanController::class, 'reports'])->name('delivery_challan.reports');
            Route::post('/delivery_challan', [DeliveryChallanController::class, 'store'])->name('delivery_challan.store');
            Route::get('/delivery_challan/edit/{v_no}', [DeliveryChallanController::class, 'edit'])->name('delivery_challan.edit');
            Route::put('/delivery_challan/update/{id}', [DeliveryChallanController::class, 'update'])->name('delivery_challan.update');
            Route::get('/delivery_challan/{v_no}/delete', [DeliveryChallanController::class, 'destroy'])->name('delivery_challan.destroy');
            Route::delete('/delivery_challan/{id}/del', [DeliveryChallanController::class, 'delete'])->name('delivery_challan.delete');

            Route::get('/delivery_challan/editCon/{v_no}', [DeliveryChallanController::class, 'editCon'])->name('delivery_challan.editDel');
            Route::put('/delivery_challan/{v_no}/updateCon', [DeliveryChallanController::class, 'updateCon'])->name('delivery_challan.updateDel');

            Route::get('/get-aid/{accountId}', [ConfectioneryController::class, 'getAid']);
            Route::get('/confectionery', [ConfectioneryController::class, 'index'])->name('confectionery.list');
            Route::post('/confectionery', [ConfectioneryController::class, 'store'])->name('confectionery.store');
            Route::get('/confectionery/reports', [ConfectioneryController::class, 'reports'])->name('confectionery.reports');
            Route::get('/confectionery/edit/{v_no}', [ConfectioneryController::class, 'edit'])->name('confectionery.edit');
            Route::put('/confectionery/update/{id}', [ConfectioneryController::class, 'update'])->name('confectionery.update');
            Route::get('/confectionery/{v_no}/delete', [ConfectioneryController::class, 'destroy'])->name('confectionery.destroy');
            Route::delete('/confectionery/{id}/del', [ConfectioneryController::class, 'delete'])->name('confectionery.delete');



            Route::get('/confectionery/editCon/{v_no}', [ConfectioneryController::class, 'editCon'])->name('confectionery.editCon');
            Route::put('/confectionery/{v_no}/updateCon', [ConfectioneryController::class, 'updateCon'])->name('confectionery.updateCon');

            Route::get('/wastage_sale', [WastageSaleController::class, 'index'])->name('wastage_sale.list');
            Route::get('/wastage_sale/reports', [WastageSaleController::class, 'reports'])->name('wastage_sale.reports');
            Route::post('/wastage_sale', [WastageSaleController::class, 'store'])->name('wastage_sale.store');
            Route::get('/wastage_sale/{v_no}/delete', [WastageSaleController::class, 'destroy'])->name('wastage_sale.destroy');
            Route::delete('/wastage_sale/{id}/delete', [WastageSaleController::class, 'delete'])->name('wastage_sale.delete');
            Route::get('/wastage_sale/edit/{v_no}', [WastageSaleController::class, 'edit'])->name('wastage_sale.edit');
            Route::put('/wastage_sale/update/{id}', [WastageSaleController::class, 'update'])->name('wastage_sale.update');



            Route::get('/dye_purchase', [DyePurchaseController::class, 'index'])->name('dye_purchase.list');
            Route::get('/dye_purchase/reports', [DyePurchaseController::class, 'reports'])->name('dye_purchases.reports');
            Route::post('/dye_purchase', [DyePurchaseController::class, 'store'])->name('dye_purchase.store');
            Route::get('/dye_purchase/{v_no}/delete', [DyePurchaseController::class, 'destroy'])->name('dye_purchase.destroy');
            Route::delete('/dye_purchase/{id}/delete', [DyePurchaseController::class, 'delete'])->name('dye_purchase.delete');
            Route::get('/dye_purchase/edit/{v_no}', [DyePurchaseController::class, 'edit'])->name('dye_purchase.edit');
            Route::put('/dye_purchase/update/{id}', [DyePurchaseController::class, 'update'])->name('dye_purchase.update');

            Route::get('/dye_purchase/editDye/{v_no}', [DyePurchaseController::class, 'editDye'])->name('dye_purchase.editDye');
            Route::put('/dye_purchase/{v_no}/updateDye', [DyePurchaseController::class, 'updateDye'])->name('dye_purchase.updateDye');


            Route::get('/wastage/reports', [WastageController::class, 'reports'])->name('wastage.reports');

            Route::get('/get-vnoss/{accountId}', [SaleInvoiceController::class, 'getVnoss']);
            Route::get('/get-entry-detailss/{vno}', [SaleInvoiceController::class, 'getEntryDetailss']);

            Route::get('/pharma_billing', [SaleInvoiceController::class, 'index'])->name('pharma_billing.list');
            Route::get('/pharma_billing/reports', [SaleInvoiceController::class, 'reports'])->name('pharma_billing.reports');
            Route::post('/pharma_billing', [SaleInvoiceController::class, 'store'])->name('pharma_billing.store');
            Route::delete('/pharma-billing/{billing_no}/del', [SaleInvoiceController::class, 'destroy'])->name('pharma_billing.destroy');


            Route::get('/get-vnos/{accountId}', [ConfectBillingController::class, 'getVnos']);
            Route::get('/get-entry-details/{vno}', [ConfectBillingController::class, 'getEntryDetails']);

            Route::get('/confect_billing', [ConfectBillingController::class, 'index'])->name('confect_billing.list');
            Route::get('/confect_billing/reports', [ConfectBillingController::class, 'reports'])->name('confect_billing.reports');
            Route::post('/confect_billing', [ConfectBillingController::class, 'store'])->name('confect_billing.store');
            Route::delete('/confect-billing/{billing_no}/del', [ConfectBillingController::class, 'destroy'])
                ->name('confect_billing.destroy');



            Route::get('/gate_pass_in', [GatePassInController::class, 'index'])->name('gate_pass_in.list');
            Route::get('/gate_pass_in/reports', [GatePassInController::class, 'reports'])->name('gate_pass_in.reports');
            Route::post('/gate_pass_in', [GatePassInController::class, 'store'])->name('gate_pass_in.store');
            Route::get('/gate_pass_in/{v_no}/delete', [GatePassInController::class, 'destroy'])->name('gate_pass_in.destroy');
            Route::delete('/gate_pass_in/{id}/delete', [GatePassInController::class, 'delete'])->name('gate_pass_in.delete');
            Route::get('/gate_pass_in/edit/{v_no}', [GatePassInController::class, 'edit'])->name('gate_pass_in.edit');
            Route::put('/gate_pass_in/update/{id}', [GatePassInController::class, 'update'])->name('gate_pass_in.update');

            Route::get('/gate_pass_out', [GatePassOutController::class, 'index'])->name('gate_pass_out.list');
            Route::get('/gate_pass_out/reports', [GatePassOutController::class, 'reports'])->name('gate_pass_out.reports');
            Route::post('/gate_pass_out', [GatePassOutController::class, 'store'])->name('gate_pass_out.store');
            Route::get('/gate_pass_out/{v_no}/delete', [GatePassOutController::class, 'destroy'])->name('gate_pass_out.destroy');
            Route::delete('/gate_pass_out/{id}/delete', [GatePassOutController::class, 'delete'])->name('gate_pass_out.delete');
            Route::get('/gate_pass_out/edit/{v_no}', [GatePassOutController::class, 'edit'])->name('gate_pass_out.edit');
            Route::put('/gate_pass_out/update/{id}', [GatePassOutController::class, 'update'])->name('gate_pass_out.update');

            Route::get('/cheque_receipts', [ChequeReceiptsController::class, 'index'])->name('cheque.index');
            Route::get('/cheque_receipts/reports', [ChequeReceiptsController::class, 'reports'])->name('cheque_receipts.reports');
            Route::post('/cheque_receipts', [ChequeReceiptsController::class, 'store'])->name('cheque_receipts.store');
            Route::delete('/cheque-receipts/{id}', [ChequeReceiptsController::class, 'destroy'])->name('chequeReceipts.destroy');
            Route::get('/cheque_receipts/edit/{v_no}', [ChequeReceiptsController::class, 'edit'])->name('cheque_receipts.edit');
            Route::put('/cheque_receipts/update/{id}', [ChequeReceiptsController::class, 'update'])->name('cheque_receipts.update');
            Route::get('/cheque_receipt/{v_no}/delete', [ChequeReceiptsController::class, 'del'])->name('cheque_receipts.del');

            Route::get('/country', [CountryController::class, 'index'])->name('country.index');
            Route::post('/country', [CountryController::class, 'store'])->name('country.store');
            Route::get('/country/add_country', [CountryController::class, 'list'])->name('country.list');
            Route::delete('/country/{id}', [CountryController::class, 'destroy'])->name('country.destroy');

            Route::get('/custom', [CustomController::class, 'index'])->name('custom.index');
            Route::post('/custom', [CustomController::class, 'store'])->name('custom.store');
            Route::get('/custom/add_country', [CustomController::class, 'list'])->name('custom.list');
            Route::delete('/custom/{id}', [CustomController::class, 'destroy'])->name('custom.destroy');

            Route::get('/printing', [DepartmentSectionController::class, 'index'])->name('print.index');
            Route::post('/printing', [DepartmentSectionController::class, 'store'])->name('print.store');
            Route::get('/printing/edit/{id}', [DepartmentSectionController::class, 'edit'])->name('print.edit');
            Route::put('/printing/{id}', [DepartmentSectionController::class, 'update'])->name('print.update');
            Route::get('/printing/add_printing', [DepartmentSectionController::class, 'list'])->name('print.list');
            Route::delete('/printing/{id}', [DepartmentSectionController::class, 'destroy'])->name('print.destroy');


            Route::get('/designation', [DesignationController::class, 'index'])->name('designation.index');
            Route::post('/designation', [DesignationController::class, 'store'])->name('designation.store');
            Route::get('/designation/edit/{id}', [DesignationController::class, 'edit'])->name('designation.edit');
            Route::put('/designation/{id}', [DesignationController::class, 'update'])->name('designation.update');
            Route::get('/designation/add_printing', [DesignationController::class, 'list'])->name('designation.list');
            Route::delete('/designation/{id}', [DesignationController::class, 'destroy'])->name('designation.destroy');


            Route::get('/extra_time', [ExtraTimeController::class, 'index'])->name('extra_time.index');
            Route::post('/extra_time', [ExtraTimeController::class, 'store'])->name('extra_time.store');
            Route::get('/extra_time/edit/{id}', [ExtraTimeController::class, 'edit'])->name('extra_time.edit');
            Route::put('/extra_time/{id}', [ExtraTimeController::class, 'update'])->name('extra_time.update');
            Route::get('/extra_time/add_printing', [ExtraTimeController::class, 'list'])->name('extra_time.list');
            Route::delete('/extra_time/{id}', [ExtraTimeController::class, 'destroy'])->name('extra_time.destroy');


            Route::get('/process', [ProcessSectionController::class, 'index'])->name('process.index');
            Route::post('/process', [ProcessSectionController::class, 'store'])->name('process.store');
            Route::get('/process/add_process', [ProcessSectionController::class, 'list'])->name('process.list');
            Route::delete('/process/{id}', [ProcessSectionController::class, 'destroy'])->name('process.destroy');

            Route::get('/paste', [PasteSectionController::class, 'index'])->name('paste.index');
            Route::post('/paste', [PasteSectionController::class, 'store'])->name('paste.store');
            Route::get('/paste/add_paste', [PasteSectionController::class, 'list'])->name('paste.list');
            Route::delete('/paste/{id}', [PasteSectionController::class, 'destroy'])->name('paste.destroy');


            Route::get('/registration_form/add_product', [RegistrationFormController::class, 'index'])->name('registration_form.list');
            Route::post('/registration_form/add_product', [RegistrationFormController::class, 'store'])->name('registration_form.store');
            Route::get('/registration_form/reports', [RegistrationFormController::class, 'reports'])->name('registration_form.reports');
            Route::get('/registration_form/edit/{id}', [RegistrationFormController::class, 'edit'])->name('registration_form.edit');
            Route::put('/registration_form/update/{id}', [RegistrationFormController::class, 'update'])->name('registration_form.update');
            Route::delete('/registration_form/{id}', [RegistrationFormController::class, 'destroy'])->name('registration_form.destroy');
            Route::delete('/registration_form/remove-image/{id}', [RegistrationFormController::class, 'removeImage'])->name('remove.image');

            Route::get('/product-log', [ProductLogController::class, 'index'])->name('product_log.index');
            Route::get('/product-log/report', [ProductLogController::class, 'report'])->name('product_log.report');

            Route::get('/profile', [UserController::class, 'profile'])->name('profile');


            Route::get('/payment_invoice', [PaymentInvoiceController::class, 'index'])->name('payment_invoice.list');
            Route::get('/payment_invoice/reports', [PaymentInvoiceController::class, 'reports'])->name('payment_invoice.reports');
            Route::post('/payment_invoice', [PaymentInvoiceController::class, 'store'])->name('payment_invoice.store');
            Route::get('/purchase-details/edit/{v_no}', [PaymentInvoiceController::class, 'edit'])->name('purchase_details.edit');
            Route::get('/purchase-details/editBoxboard/{v_no}', [PaymentInvoiceController::class, 'editBoxboard'])->name('purchase_details.editBoxboard');
            Route::put('/purchase-details/{v_no}/updateBoxboard', [PaymentInvoiceController::class, 'updateBoxboard'])->name('purchase_details.updateBoxboard');
            Route::put('/purchase-details/{v_no}/update', [PaymentInvoiceController::class, 'update'])->name('purchase_details.update');
            Route::delete('/purchase-details/{id}/delete', [PaymentInvoiceController::class, 'destroy'])->name('purchase_details.delete');
            Route::get('/purchase-details/{id}/del', [PaymentInvoiceController::class, 'delete'])->name('purchase_details.destroy');

            Route::get('/purchase_return', [PurchaseReturnController::class, 'index'])->name('purchase_return.list');
            Route::post('/purchase_return', [PurchaseReturnController::class, 'store'])->name('purchase_return.store');
            Route::get('/purchase_return/reports', [PurchaseReturnController::class, 'reports'])->name('purchase_return.reports');
            Route::get('/purchase_return/{id}/delete', [PurchaseReturnController::class, 'destroy'])->name('purchase_return.destroy');
            Route::delete('/purchase_return/{id}/del', [PurchaseReturnController::class, 'delete'])->name('purchase_return.delete');
            Route::get('/purchase_return/edit/{v_no}', [PurchaseReturnController::class, 'edit'])->name('purchase_return.edit');
            Route::put('/purchase_return/update/{id}', [PurchaseReturnController::class, 'update'])->name('purchase_return.update');

            Route::get('/plate_purchase', [PlatePurchaseController::class, 'index'])->name('plate_purchase.list');
            Route::get('/plate_purchase/reports', [PlatePurchaseController::class, 'reports'])->name('plate_purchase.reports');
            Route::get('/get-products-by-country', [PlatePurchaseController::class, 'getProductsByCountry']);
            Route::post('/plate_purchase', [PlatePurchaseController::class, 'store'])->name('plate_purchase.store');
            Route::get('/plate_purchase/edit/{v_no}', [PlatePurchaseController::class, 'edit'])->name('plate_purchase.edit');
            Route::put('/plate_purchase/update/{id}', [PlatePurchaseController::class, 'update'])->name('plate_purchase.update');
            Route::get('/plate_purchase/{v_no}/delete', [PlatePurchaseController::class, 'destroy'])->name('plate_purchase.destroy');
            Route::delete('/plate_purchase/{id}/del', [PlatePurchaseController::class, 'delete'])->name('plate_purchase.delete');



            Route::get('/plate_return', [PlateReturnController::class, 'index'])->name('plate_return.list');
            Route::get('/plate_return/reports', [PlateReturnController::class, 'reports'])->name('plate_return.reports');
            Route::post('/plate_return', [PlateReturnController::class, 'store'])->name('plate_return.store');
            Route::delete('/plate_return/{id}', [PlateReturnController::class, 'destroy'])->name('plate_return.destroy');

            Route::get('/glue_purchase', [GluePurchaseController::class, 'index'])->name('glue_purchase.list');
            Route::get('/glue_purchase/reports', [GluePurchaseController::class, 'reports'])->name('glue_purchase.reports');
            Route::post('/glue_purchase', [GluePurchaseController::class, 'store'])->name('glue_purchase.store');
            Route::get('/glue_purchase/edit/{v_no}', [GluePurchaseController::class, 'edit'])->name('glue_purchase.edit');
            Route::put('/glue_purchase/update/{id}', [GluePurchaseController::class, 'update'])->name('glue_purchase.update');
            Route::get('/glue_purchase/{v_no}/delete', [GluePurchaseController::class, 'destroy'])->name('glue_purchase.destroy');
            Route::delete('/glue_purchase/{id}/del', [GluePurchaseController::class, 'delete'])->name('glue_purchase.delete');
            Route::get('/glue_purchase/editBoxboard/{v_no}', [GluePurchaseController::class, 'editBoxboard'])->name('glue_purchase.editBoxboard');
            Route::put('/glue_purchase/{v_no}/updateBoxboard', [GluePurchaseController::class, 'updateBoxboard'])->name('glue_purchase.updateBoxboard');


            Route::get('/glue_return', [GlueReturnController::class, 'index'])->name('glue_return.list');
            Route::get('/glue_return/reports', [GlueReturnController::class, 'reports'])->name('glue_return.reports');
            Route::post('/glue_return', [GlueReturnController::class, 'store'])->name('glue_return.store');
            Route::delete('/glue_return/{id}', [GlueReturnController::class, 'destroy'])->name('glue_return.destroy');




            Route::get('/ink_purchase', [InkPurchaseController::class, 'index'])->name('ink_purchase.list');
            Route::get('/ink_purchase/reports', [InkPurchaseController::class, 'reports'])->name('ink_purchase.reports');
            Route::post('/ink_purchase', [InkPurchaseController::class, 'store'])->name('ink_purchase.store');
            Route::get('/ink_purchase/edit/{v_no}', [InkPurchaseController::class, 'edit'])->name('ink_purchase.edit');
            Route::put('/ink_purchase/update/{id}', [InkPurchaseController::class, 'update'])->name('ink_purchase.update');
            Route::get('/ink_purchase/{v_no}/delete', [InkPurchaseController::class, 'destroy'])->name('ink_purchase.destroy');
            Route::delete('/ink_purchase/{id}/del', [InkPurchaseController::class, 'delete'])->name('ink_purchase.delete');
            Route::get('/ink_purchase/editBoxboard/{v_no}', [InkPurchaseController::class, 'editBoxboard'])->name('ink_purchase.editBoxboard');
            Route::put('/ink_purchase/{v_no}/updateBoxboard', [InkPurchaseController::class, 'updateBoxboard'])->name('ink_purchase.updateBoxboard');



            Route::get('/ink_return', [InkReturnController::class, 'index'])->name('ink_return.list');
            Route::get('/ink_return/reports', [InkReturnController::class, 'reports'])->name('ink_return.reports');
            Route::post('/ink_return', [InkReturnController::class, 'store'])->name('ink_return.store');
            Route::delete('/ink_return/{id}', [InkReturnController::class, 'destroy'])->name('ink_return.destroy');



            Route::get('/shippers_purchase', [ShipperPurchasesController::class, 'index'])->name('shipper_purchases.list');
            Route::get('/shippers_purchase/reports', [ShipperPurchasesController::class, 'reports'])->name('shipper_purchases.reports');
            Route::post('/shippers_purchase', [ShipperPurchasesController::class, 'store'])->name('shipper_purchases.store');
            Route::get('/shippers_purchase/edit/{v_no}', [ShipperPurchasesController::class, 'edit'])->name('shipper_purchases.edit');
            Route::put('/shippers_purchase/update/{id}', [ShipperPurchasesController::class, 'update'])->name('shipper_purchases.update');
            Route::get('/shippers_purchase/{v_no}/delete', [ShipperPurchasesController::class, 'destroy'])->name('shipper_purchases.destroy');
            Route::delete('/shippers_purchase/{id}/del', [ShipperPurchasesController::class, 'delete'])->name('shipper_purchases.delete');
            Route::get('/shippers_purchase/editBoxboard/{v_no}', [ShipperPurchasesController::class, 'editBoxboard'])->name('shipper_purchases.editBoxboard');
            Route::put('/shippers_purchase/{v_no}/updateBoxboard', [ShipperPurchasesController::class, 'updateBoxboard'])->name('shipper_purchases.updateBoxboard');


            Route::get('/shipper_return', [ShipperReturnController::class, 'index'])->name('shipper_return.list');
            Route::get('/shipper_return/reports', [ShipperReturnController::class, 'reports'])->name('shipper_return.reports');
            Route::post('/shipper_return', [ShipperReturnController::class, 'store'])->name('shipper_return.store');
            Route::delete('/shipper_return/{id}', [ShipperReturnController::class, 'destroy'])->name('shipper_return.destroy');


            Route::get('/dye_return', [DyeReturnController::class, 'index'])->name('dye_return.list');
            Route::get('/dye_return/reports', [DyeReturnController::class, 'reports'])->name('dye_return.reports');
            Route::post('/dye_return', [DyeReturnController::class, 'store'])->name('dye_return.store');
            Route::delete('/dye_return/{id}', [DyeReturnController::class, 'destroy'])->name('dye_return.destroy');



            Route::get('/lemination_purchase', [LeminationPurchaseController::class, 'index'])->name('lemination_purchase.list');
            Route::get('/lemination_purchase/reports', [LeminationPurchaseController::class, 'reports'])->name('lemination_purchase.reports');
            Route::post('/lemination_purchase', [LeminationPurchaseController::class, 'store'])->name('lemination_purchase.store');
            Route::get('/lemination_purchase/edit/{v_no}', [LeminationPurchaseController::class, 'edit'])->name('lemination_purchase.edit');
            Route::put('/lemination_purchase/update/{id}', [LeminationPurchaseController::class, 'update'])->name('lemination_purchase.update');
            Route::get('/lemination_purchase/{v_no}/delete', [LeminationPurchaseController::class, 'destroy'])->name('lemination_purchase.destroy');
            Route::delete('/lemination_purchase/{id}/del', [LeminationPurchaseController::class, 'delete'])->name('lemination_purchase.delete');
            Route::get('/lemination_purchase/editBoxboard/{v_no}', [LeminationPurchaseController::class, 'editBoxboard'])->name('lemination_purchase.editBoxboard');
            Route::put('/lemination_purchase/{v_no}/updateBoxboard', [LeminationPurchaseController::class, 'updateBoxboard'])->name('lemination_purchase.updateBoxboard');







            Route::get('/lamination_return', [LaminationReturnController::class, 'index'])->name('lamination_return.list');
            Route::get('/lamination_return/reports', [LaminationReturnController::class, 'reports'])->name('lamination_return.reports');
            Route::post('/lamination_return', [LaminationReturnController::class, 'store'])->name('lamination_return.store');
            Route::delete('/lamination_return/{id}', [LaminationReturnController::class, 'destroy'])->name('lamination_return.destroy');





            Route::get('/corrugation_return', [CorrugationReturnController::class, 'index'])->name('corrugation_return.list');
            Route::get('/corrugation_return/reports', [CorrugationReturnController::class, 'reports'])->name('corrugation_return.reports');
            Route::post('/corrugation_return', [CorrugationReturnController::class, 'store'])->name('corrugation_return.store');
            Route::delete('/corrugation_return/{id}', [CorrugationReturnController::class, 'destroy'])->name('corrugation_return.destroy');




            Route::get('/corrugation_purchase', [CorrugationPurchaseController::class, 'index'])->name('corrugation_purchase.list');
            Route::get('/corrugation_purchase/reports', [CorrugationPurchaseController::class, 'reports'])->name('corrugation_purchase.reports');
            Route::post('/corrugation_purchase', [CorrugationPurchaseController::class, 'store'])->name('corrugation_purchase.store');
            Route::get('/corrugation_purchase/edit/{v_no}', [CorrugationPurchaseController::class, 'edit'])->name('corrugation_purchase.edit');
            Route::put('/corrugation_purchase/update/{id}', [CorrugationPurchaseController::class, 'update'])->name('corrugation_purchase.update');
            Route::get('/corrugation_purchase/{v_no}/delete', [CorrugationPurchaseController::class, 'destroy'])->name('corrugation_purchase.destroy');
            Route::delete('/corrugation_purchase/{id}/del', [CorrugationPurchaseController::class, 'delete'])->name('corrugation_purchase.delete');
            Route::get('/corrugation_purchase/editBoxboard/{v_no}', [CorrugationPurchaseController::class, 'editBoxboard'])->name('corrugation_purchase.editBoxboard');
            Route::put('/corrugation_purchase/{v_no}/updateBoxboard', [CorrugationPurchaseController::class, 'updateBoxboard'])->name('corrugation_purchase.updateBoxboard');
            Route::resource('/saleotype', SaleTypeController::class)->names('saletype');
            Route::resource('/customers', Customer::class)->names(names: 'customers');
            Route::get('/customers-by-acc/{accId}', [Customer::class, 'getByAccId']);

            Route::resource('/purchase_invoices', PurchaseInvoiceController::class)->names('purchase_invoices');
            Route::get('/item-based-purchase', [PurchaseInvoiceController::class, 'report'])->name('purchase_invoices.report');
            Route::get('/purchase-report', function() {
                return redirect()->route('purchase_invoices.report');
            });
            Route::resource('/purchase_return_invoices', PurchaseReturnInvoiceController::class)->names('purchase_return_invoices');
            Route::resource('/sale_return_invoices', SaleReturnInvoiceController::class)->names('sale_return_invoices');
            Route::resource('/openingStock', OpeningStockController::class)->names('openingStock');


            Route::resource('/suppliers', SupplierController::class)->names('suppliers');
            Route::resource('/contractors', ContractorController::class)->except(['show'])->names('contractors');
            Route::resource('/transport-companies', TransportCompanyController::class)->names('transport_companies')->except(['show']);
            Route::resource('/reference-names', ReferenceNamesController::class)->names('ReferenceNames')->except(['show']);
            Route::resource('/consumption', controller: ConsumptionController::class)->names('consumption')->except(['show']);
            Route::resource('/consumption2', controller: Consumption2Controller::class)->names('consumption2')->except(['show']);
            Route::get('/stock-in/get-product-data', [StockInController::class, 'getProductData'])->name('stock-in.get-product-data');
            Route::resource('/stock-in', StockInController::class)->names('stock-in')->except(['show']);
            Route::get('/stock-in/{id}/print', [StockInController::class, 'print'])->name('stock-in.print');
            Route::get('/lot-in-report', [LotInReportController::class, 'index'])->name('lot-in-report.index');
            Route::get('/lot-in-report/print', [LotInReportController::class, 'print'])->name('lot-in-report.print');
            Route::resource('/stock-issue', StockIssueController::class)->names('stock-issue')->except(['show']);
            Route::get('/stock-issue/get-mold-qty/{productId}/{itemId}', [StockIssueController::class, 'getMoldQty']);
            Route::get('/stock-issue/items-by-product/{productId}', [StockIssueController::class, 'getItemsByProduct']);
            Route::resource('/contractor-lot-posting', ContractorLotPostingController::class)->names('contractor-lot-posting')->except(['show']);
            Route::get('/contractor-lot-posting/items-by-product/{productId}', [ContractorLotPostingController::class, 'getItemsByProduct']);
            Route::resource('/packing-lot-posting', PackingLotPostingController::class)->names('packing-lot-posting')->except(['show']);
            Route::get('/packing-lot-posting/items-by-product/{productId}', [PackingLotPostingController::class, 'getItemsByProduct']);
            Route::resource('/sale_invoices', SaleInvoices::class)->names('sale_invoices')->except(['show']);
            Route::get('/get-dcs-by-customer/{customerId}', [SaleInvoices::class, 'getDcsByCustomer']);
            Route::get('/get-dc-items/{v_no}', [SaleInvoices::class, 'getDcItems']);
            Route::get('/historical-rates/import', [HistoricalRateImportController::class, 'index'])->name('historical_rates.import');
            Route::post('/historical-rates/import', [HistoricalRateImportController::class, 'import'])->name('historical_rates.import.post');
            Route::post('/historical-rates/clear', [HistoricalRateImportController::class, 'clear'])->name('historical_rates.clear');
            Route::prefix('sale-order')->name('sale_order.')->group(function () {
                Route::get('/', [SaleOrderController::class, 'index'])->name('index');
                Route::get('/create', [SaleOrderController::class, 'create'])->name('create');
                Route::post('/', [SaleOrderController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [SaleOrderController::class, 'edit'])->name('edit');
                Route::put('/{id}', [SaleOrderController::class, 'update'])->name('update');
                Route::delete('/{id}', [SaleOrderController::class, 'destroy'])->name('destroy');
                Route::get('/{id}/print', [SaleOrderController::class, 'print'])->name('print');
                Route::get('/customer-orders/{customerId}', [SaleOrderController::class, 'getCustomerOrders'])->name('customer_orders');
            });
            Route::prefix('sale-order-dc')->name('sale_order_dc.')->group(function () {
                Route::get('/', [SaleOrderDcController::class, 'index'])->name('index');
                Route::get('/create', [SaleOrderDcController::class, 'create'])->name('create');
                Route::post('/', [SaleOrderDcController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [SaleOrderDcController::class, 'edit'])->name('edit');
                Route::put('/{id}', [SaleOrderDcController::class, 'update'])->name('update');
                Route::delete('/{id}', [SaleOrderDcController::class, 'destroy'])->name('destroy');
                Route::get('/{id}/print', [SaleOrderDcController::class, 'print'])->name('print');
            });
            Route::get('/get-sors-by-customer/{customerId}', [SaleOrderDcController::class, 'getSorsByCustomer']);
            Route::get('/get-sor-items/{v_no}', [SaleOrderDcController::class, 'getSorItems']);
            Route::get('/get-sor-master/{v_no}', [SaleOrderDcController::class, 'getSorMaster']);
            Route::get('/get-consumption-qpc/{productId}', [SaleOrderDcController::class, 'getConsumptionQpc']);
            Route::get('/get-next-lot-no/{itemId}', [SaleOrderDcController::class, 'getNextLotNo']);
            Route::get('/get-next-purchase-lot-no/{itemId}', [PurchaseInvoiceController::class, 'getNextPurchaseLotNo']);
            Route::get('/itemwisesale', [ItemWiseController::class, 'show'])->name('ItemReportbysale.index');
            Route::get('/typewisesale', [TypeWiseController::class, 'type'])->name('ItemReportbysale.type');
            Route::get('/customerwise-report', [CustomerWiseReportController::class, 'index'])
                ->name('customerwise.report');
            Route::get('/salewise-report', [CustomerWiseReportController::class, 'saleWise'])
                ->name('saleWise.report');
            Route::get('cashreports/byUsers', [CashReciptReport::class, 'index'])->name('cashreport.users');
            Route::get('/Puritemwisesale', action: [ReportPurchaser::class, 'ItemWise'])->name('ItemReportbysalePur.index');
            Route::get('/typewisePurchase', [ReportPurchaser::class, 'type'])->name('ItemReportbyPurchase.type');

            // Route::get('/typewisePurchase',[ReportPurchaser::class,'type'])->name('ItemReportbyPurchase.type');
            Route::get('/supplierwise-report', [ReportPurchaser::class, 'supplierwise'])
                ->name('Supplierwise.report');
            Route::get('/Purchasewise-report', [ReportPurchaser::class, 'saleWise'])
                ->name('PurchaseWise.report');

            Route::get('/itemSelected/{id}/{customerId}', [ItemSelectedController::class, 'sale']);
            Route::get('/itemSelectedR/{id}/{customerId}', [ItemSelectedController::class, 'saleReturn']);
            Route::get('/itemSelectedP/{id}/{customerId}', [ItemSelectedController::class, 'purchase']);
            Route::get('/itemSelectedPR/{id}/{customerId}', [ItemSelectedController::class, 'purchaseReturn']);

            Route::get('/register', [RegisterUser::class, 'create'])->name('register');
            Route::post('/register', [RegisterUser::class, 'store'])->name('register.user');
        });


        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);

        // Auth::routes(['login' => false, 'register' => false, 'logout' => false]);

        // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        // Route::get('/checker', [App\Http\Controllers\ItemSelectedController::class, 'checker'])->name('homeChecker');

        Route::resource('/sale-return-invoices', UserController::class)->names('users');

        Route::resource('users', UserManageController::class)->names('userssingle');
        Route::prefix('permissions')->group(function () {
            Route::get('/', [UserPermissionManage::class, 'index'])->name('user_permissions.index');
            Route::get('/user/{userId}', [UserPermissionManage::class, 'getUserPermissions'])->name('user_permissions.get');
            Route::post('/store', [UserPermissionManage::class, 'store'])->name('user_permissions.store');
        });
        Route::get('/auto-login-debug', function () {
            // Step 1: Check environment
            echo "1. Environment: " . app()->environment() . "<br>";

            if (!app()->environment('local')) {
                abort(404);
            }

            // Step 2: Check if user exists or create
            $email = 'superadmin@example.com';
            $password = '11221122';

            $user = \App\Models\User::where('email', $email)->first();
            echo "2. User exists: " . ($user ? 'YES' : 'NO') . "<br>";

            if (!$user) {
                $user = \App\Models\User::create([
                    'name' => 'Demo User',
                    'email' => $email,
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                ]);
                echo "3. User created<br>";
            }

            // Step 3: Try to login
            echo "4. Attempting login...<br>";
            \Illuminate\Support\Facades\Auth::login($user);

            echo "5. Auth check: " . (\Illuminate\Support\Facades\Auth::check() ? 'YES' : 'NO') . "<br>";
            echo "6. User ID: " . (\Illuminate\Support\Facades\Auth::id() ?: 'NULL') . "<br>";

            // Step 4: Regenerate session
            session()->regenerate();

            // Step 5: Redirect
            echo "<br>Redirecting to /admin/dashboard in 3 seconds...";
            echo "<script>setTimeout(function() { window.location.href = 'admin/dashboard'; }, 3000);</script>";

            return; // Let JavaScript handle the redirect
        });
    }
);