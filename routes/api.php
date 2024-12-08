<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => ['cors', 'json.response']], function () {

    Route::post('/auth/login', [\App\Http\Controllers\API\Auth\AccessTokenController::class, 'issueToken']);

    Route::post('/auth/register', [\App\Http\Controllers\API\Auth\AuthController::class, 'register']);

    Route::post('/auth/forgot-password', [\App\Http\Controllers\API\Auth\AuthController::class, 'forgotPassword']);

    Route::get('/auth/reset-password/{token}', [\App\Http\Controllers\API\Auth\AuthController::class, 'returnToken'])->name('password.reset');

    Route::post('/auth/reset-password/{token}', [\App\Http\Controllers\API\Auth\AuthController::class, 'resetPassword']);

    Route::post('/auth/verify/{token}', [\App\Http\Controllers\API\Auth\AuthController::class, 'verifyEmail']);
});

Route::middleware('auth:api')->group(function () {

    Route::resource('roles', App\Http\Controllers\API\RoleAPIController::class);

    Route::resource('status', App\Http\Controllers\API\StatusAPIController::class);

    Route::resource('users', App\Http\Controllers\API\UserAPIController::class);

    Route::resource('services', App\Http\Controllers\API\ServiceAPIController::class);

    Route::resource('farms', App\Http\Controllers\API\FarmAPIController::class);

    Route::resource('products', App\Http\Controllers\API\ProductAPIController::class);

    Route::resource('farm-activities', App\Http\Controllers\API\FarmActivityAPIController::class);

    Route::resource('rate-cards', App\Http\Controllers\API\RateCardAPIController::class);

    Route::resource('loan-packages', App\Http\Controllers\API\LoanPackageAPIController::class);

    Route::resource('wallets', App\Http\Controllers\API\WalletAPIController::class);

    Route::resource('wallet-transactions', App\Http\Controllers\API\WalletTransactionAPIController::class);

    Route::get('data/wallet-statements', [App\Http\Controllers\API\WalletTransactionAPIController::class, 'getWalletStatements']);

    Route::post('loans/update/date/{cohort_id}', [App\Http\Controllers\API\LoanAPIController::class, 'approveLoanByCohort']);

    Route::resource('loans', App\Http\Controllers\API\LoanAPIController::class);

    Route::resource('loan-items', App\Http\Controllers\API\LoanItemAPIController::class);

    Route::resource('deposits', App\Http\Controllers\API\DepositAPIController::class);

    Route::resource('modules', App\Http\Controllers\API\ModuleAPIController::class);

    Route::resource('permissions', App\Http\Controllers\API\PermissionAPIController::class);

    Route::resource('role-permissions', App\Http\Controllers\API\RolePermissionAPIController::class);

    Route::resource('farm-activities', App\Http\Controllers\API\FarmActivityAPIController::class);

    Route::post('update-farm-activities', [App\Http\Controllers\API\FarmActivityAPIController::class, 'update']);

    Route::put('approve-farm-activity/{id}', [App\Http\Controllers\API\FarmActivityAPIController::class, 'approveFarmActivity']);

    Route::post('farmer-loan/{id}', [App\Http\Controllers\API\ReportAPIController::class, 'farmersLoan']);

    Route::post('cohort-loan/{id}', [App\Http\Controllers\API\ReportAPIController::class, 'cohortLoan']);

    Route::post('approve-farm-activities', [App\Http\Controllers\API\FarmActivityAPIController::class, 'approveFarmActivities']);

    Route::resource('farm-activity-items', App\Http\Controllers\API\FarmActivityItemAPIController::class);

    Route::resource('countries', App\Http\Controllers\API\CountryAPIController::class);

    Route::resource('administration-level-ones', App\Http\Controllers\API\AdministrationLevelOneAPIController::class);

    Route::resource('administration-level-twos', App\Http\Controllers\API\AdministrationLevelTwoAPIController::class);

    Route::resource('administration-level-threes', App\Http\Controllers\API\AdministrationLevelThreeAPIController::class);

    Route::resource('units', App\Http\Controllers\API\UnitAPIController::class);

    Route::resource('relationships', App\Http\Controllers\API\RelationshipAPIController::class);

    Route::resource('cohorts', App\Http\Controllers\API\CohortAPIController::class);

    Route::get('farm-activities/print-statement/{id}', [App\Http\Controllers\API\FarmActivityAPIController::class, 'print_statement']);

    Route::post('farm-activities/multi-print-statement', [App\Http\Controllers\API\FarmActivityAPIController::class, 'multi_print_statement']);

    Route::resource('farm-activities', App\Http\Controllers\API\FarmActivityAPIController::class);

    Route::resource('farm-activity-items', App\Http\Controllers\API\FarmActivityItemAPIController::class);

    Route::resource('farmers', App\Http\Controllers\API\FarmerAPIController::class);

    Route::post('update-farmers/{id}', [App\Http\Controllers\API\FarmerAPIController::class, 'update']);

    Route::resource('kin', App\Http\Controllers\API\KinAPIController::class);

    Route::post('users/change-password', [App\Http\Controllers\API\UserAPIController::class, 'changePassword']);

    Route::resource('site-visits', App\Http\Controllers\API\SiteVisitAPIController::class);

    Route::resource('actions', App\Http\Controllers\API\ActionAPIController::class);

    Route::resource('payment-modes', App\Http\Controllers\API\PaymentModeController::class);

    Route::resource('payments', App\Http\Controllers\API\PaymentAPIController::class);

    Route::resource('loan-payments', App\Http\Controllers\API\LoanPaymentAPIController::class);

    Route::resource('categories', App\Http\Controllers\API\CategoryAPIController::class);

    Route::post('import-products', [App\Http\Controllers\API\ProductAPIController::class, 'importProducts']);

    Route::resource('settings', App\Http\Controllers\API\SettingAPIController::class);

    Route::post('update/settings/{id}', [App\Http\Controllers\API\SettingAPIController::class, 'update']);

    Route::resource('centers', App\Http\Controllers\API\CenterAPIController::class);

    Route::resource('activity-logs', App\Http\Controllers\API\ActivityLogAPIController::class);

    Route::resource('harvests', App\Http\Controllers\API\HarvestAPIController::class);

    Route::get('statistics', [App\Http\Controllers\API\ReportAPIController::class, 'statistics']);

    Route::post('reports/yield', [App\Http\Controllers\API\ReportAPIController::class, 'downloadYieldReportExcel']);

    Route::post('data/yields', [App\Http\Controllers\API\ReportAPIController::class, 'yieldsReport']);

    Route::post('reports/loan', [App\Http\Controllers\API\ReportAPIController::class, 'downloadLoanReportExcel']);

    Route::post('data/loans ', [App\Http\Controllers\API\ReportAPIController::class, 'loansReport']);

    Route::post('export-wallet-transactions ', [App\Http\Controllers\API\ReportAPIController::class, 'walletStatement']);




});

