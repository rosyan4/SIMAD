<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Opd\OPDDashboardController;
use App\Http\Controllers\Opd\OPDProfileController;
use App\Http\Controllers\Opd\OPDAssetController;
use App\Http\Controllers\Opd\OPDMasterController;
use App\Http\Controllers\Opd\OPDTransactionController;
// ==================== AUTHENTICATION ====================
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware(['auth'])->group(function () {

    // ==================== ROOT REDIRECT BASED ON ROLE ====================
    Route::get('/', function () {
        $user = auth()->user();

        if ($user->isAdminUtama()) {
            return redirect()->route('admin.dashboard.index');
        }

        return redirect()->route('opd.dashboard.index');
    })->name('home');


    // ==================== ADMIN OPD ROUTES ====================
    Route::prefix('opd')->name('opd.')->middleware(['auth', 'admin.opd'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [OPDDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/chart-data', [OPDDashboardController::class, 'chartData'])->name('dashboard.chartData');
        Route::get('/dashboard/maintenance-stats', [OPDDashboardController::class, 'maintenanceStats'])->name('dashboard.maintenance-stats');
        
        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [OPDProfileController::class, 'index'])->name('index');
            Route::put('/', [OPDProfileController::class, 'update'])->name('update');
            Route::post('/change-password', [OPDProfileController::class, 'changePassword'])->name('changePassword');
            Route::put('/opd-profile', [OPDProfileController::class, 'updateOpdProfile'])->name('updateOpd');
            Route::put('/notifications', [OPDProfileController::class, 'updateNotifications'])->name('updateNotifications');
            Route::get('/user-stats', [OPDProfileController::class, 'getUserStats'])->name('userStats');
            Route::get('/opd-stats', [OPDProfileController::class, 'getOpdStats'])->name('opdStats');
        });
        
        // Asset Management
        Route::prefix('assets')->name('assets.')->group(function () {
            Route::get('/', [OPDAssetController::class, 'index'])->name('index');
            Route::get('/create', [OPDAssetController::class, 'create'])->name('create');
            Route::post('/', [OPDAssetController::class, 'store'])->name('store');
            Route::get('/{asset}', [OPDAssetController::class, 'show'])->name('show');
            Route::get('/{asset}/edit', [OPDAssetController::class, 'edit'])->name('edit');
            Route::put('/{asset}', [OPDAssetController::class, 'update'])->name('update');
            Route::delete('/{asset}', [OPDAssetController::class, 'destroy'])->name('destroy');
            
            // AJAX Routes
            Route::post('/preview-code', [OPDAssetController::class, 'previewAssetCode'])->name('previewCode');
            Route::put('/{asset}/update-field', [OPDAssetController::class, 'updateField'])->name('updateField');
            Route::post('/{asset}/upload-document', [OPDAssetController::class, 'uploadDocument'])->name('uploadDocument');
            Route::delete('/documents/{document}', [OPDAssetController::class, 'deleteDocument'])->name('deleteDocument');
            Route::get('/stats', [OPDAssetController::class, 'getStats'])->name('stats');
            Route::get('/export', [OPDAssetController::class, 'export'])->name('export');
            Route::post('/bulk-action', [OpdAssetController::class, 'bulkAction'])->name('bulkAction');
        });
        
        // Master Data Management
        Route::prefix('master')->name('master.')->group(function () {
        
            // Halaman utama master data dengan sistem tab
            Route::get('/', [OPDMasterController::class, 'index'])->name('index');
            
            // CRUD Lokasi
            Route::post('/location', [OPDMasterController::class, 'locationStore'])->name('locationStore');
            Route::put('/location/{location}', [OPDMasterController::class, 'locationUpdate'])->name('locationUpdate');
            Route::delete('/location/{location}', [OPDMasterController::class, 'locationDestroy'])->name('locationDestroy');
            
            // AJAX Endpoints untuk Lokasi
            Route::get('/location/{location}', [OPDMasterController::class, 'getLocation'])->name('getLocation');
            Route::get('/location-stats', [OPDMasterController::class, 'getLocationStats'])->name('getLocationStats');
            Route::get('/search-locations', [OPDMasterController::class, 'searchLocations'])->name('location.search');
            
            // Pindahkan aset ke lokasi lain
            Route::post('/move-asset/{asset}', [OPDMasterController::class, 'moveAsset'])->name('asset.move');
            
        });
        
        // Transaction Management
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [OPDTransactionController::class, 'index'])->name('index');
            Route::get('/create', [OPDTransactionController::class, 'create'])->name('create');
            
            // Store Routes by Type
            Route::post('/deletions', [OPDTransactionController::class, 'storeDeletion'])->name('deletions.store');
            Route::post('/mutations', [OPDTransactionController::class, 'storeMutation'])->name('storeMutation');
            Route::post('/maintenances', [OPDTransactionController::class, 'storeMaintenance'])->name('storeMaintenance');
            
            // Show/Cancel by Type
            Route::get('/{type}/{id}', [OPDTransactionController::class, 'show'])->where('type', 'deletion|mutation|maintenance')->name('show');
            Route::post('/{type}/{id}/cancel', [OPDTransactionController::class, 'cancel'])->where('type', 'deletion|mutation|maintenance')->name('cancel');
            
            // Maintenance specific
            Route::put('/maintenances/{maintenance}/status', [OPDTransactionController::class, 'updateMaintenanceStatus'])->name('maintenances.update-status');
            
            // AJAX Routes
            Route::post('/mutations/{mutation}/accept', [OPDTransactionController::class, 'acceptMutation'])->name('mutations.accept');
            Route::get('/statistics', [OPDTransactionController::class, 'getStatistics'])->name('statistics');
        });
    });
});

// ==================== PUBLIC ROUTES ====================
Route::get('/welcome', function () {
    return view('auth.login');
})->middleware('guest')->name('login');