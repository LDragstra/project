<?php

use App\Http\Controllers\BonnenController;
use App\Http\Controllers\FactorController;
use App\Http\Controllers\FactuurController;
use App\Http\Controllers\ProjectController;

Auth::routes(['register' => false, 'password.request' => false, 'password.reset' => false]);

Route::group(['middleware' => ['auth', 'admin', 'active', 'checkCompanySession']], function () {
    Route::get('/', \HomepageController::class)->name('home');
    Route::get('/home', \HomepageController::class);
    Route::get('/bedrijf/{id}', \SwitchCompanyController::class)->name('bedrijf');
    Route::get('/getCharts', \HomeChartController::class)->name('charts');
    Route::get('/bonnen', [BonnenController::class, 'index'])->name('bonnen');
    Route::get('/bonnen/cache', [BonnenController::class, 'cache'])->name('bonnenCache');
    Route::post('/markReceipt', [BonnenController::class, 'markReceiptAsDone'])->name('markReceipt');
    Route::post('/factuur/{bon}', [FactuurController::class, 'done'])->name('doneFactuur');
    Route::get('/factuur/delete/{id}', [FactuurController::class, 'delete'])->name('deleteFactuur');
    Route::get('/factuur/{factuur}', [FactuurController::class, 'show'])->name('showFactuur');
    Route::get('/facturen', [FactuurController::class, 'index'])->name('facturen');
    Route::post('/factuurVersturen', [FactuurController::class, 'mail'])->name('sendMail');
    Route::post('/addOrder', [FactuurController::class, 'addInvoiceOrder'])->name('createInvoiceOrder');
    Route::get('/documenten', [FactuurController::class, 'setDocument'])->name('setDocument');
    Route::get('/factuurnummer', [FactuurController::class, 'getInvoiceNumber'])->name('factuurnummer');
    Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project');
    Route::get('/projecten', [ProjectController::class, 'index'])->name('projecten');
    Route::get('/factoring', [FactorController::class, 'index'])->name('factoring');
    Route::get('/factoring/initiele-betaling', [FactorController::class, 'factoringPayment'])->name('initiele-betaling');
    Route::post('/factoring/initial-payment', [FactorController::class, 'factoringPaid'])->name('initial-payment');
    Route::get('/factoring/initial-payment/delete/{id}', [FactorController::class, 'destroy'])->name('delete-initial-payment');
    Route::get('/factoring/restant-betaling', [FactorController::class, 'factoringRestPayment'])->name('restant-betaling');
    Route::post('/factoring/rest-payment', [FactorController::class, 'factoringRestPaid'])->name('rest-payment');
    Route::get('/factoring/betalingen', [FactorController::class, 'factoringPayments'])->name('factor-betalingen');
});

Route::any('{all}', function () {
    if ($user = Auth::user()) {
        return redirect()->route('home')->with('status', 'Pagina bestaat niet.');
    }
    return redirect()->route('login')->with('status', 'Pagina bestaat niet');
})->where('all', '.*');
