<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Opd\OpdDashboardController;
use App\Http\Controllers\Opd\OpdProfileController;
use App\Http\Controllers\Opd\OpdAssetController;
use App\Http\Controllers\Opd\OpdMasterController;
use App\Http\Controllers\Opd\OpdTransactionController;
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
        Route::controller(OpdDashboardController::class)->prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/chart-data', 'chartData')->name('chartData');
            Route::get('/maintenance-stats', 'maintenanceStats')->name('maintenanceStats');
        });
        
        // Profile Management
        Route::controller(OpdProfileController::class)->prefix('profile')->name('profile.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/', 'update')->name('update');
            Route::post('/change-password', 'changePassword')->name('changePassword');
            Route::put('/opd-profile', 'updateOpdProfile')->name('updateOpdProfile');
            Route::put('/notifications', 'updateNotifications')->name('updateNotifications');
            Route::get('/user-stats', 'getUserStats')->name('userStats');
            Route::get('/opd-stats', 'getOpdStats')->name('opdStats');
        });
        
        // Asset Management
        Route::controller(OpdAssetController::class)->prefix('assets')->name('assets.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{asset}', 'show')->name('show');
            Route::get('/{asset}/edit', 'edit')->name('edit');
            Route::put('/{asset}', 'update')->name('update');
            Route::delete('/{asset}', 'destroy')->name('destroy');
            
            // AJAX Routes
            Route::post('/preview-code', 'previewAssetCode')->name('previewAssetCode');
            Route::put('/{asset}/update-field', 'updateField')->name('updateField');
            Route::post('/{asset}/upload-document', 'uploadDocument')->name('uploadDocument');
            Route::delete('/documents/{document}', 'deleteDocument')->name('deleteDocument');
            Route::get('/stats', 'getStats')->name('stats');
            Route::get('/export', 'export')->name('export');
        });
        
        // Master Data Management
        Route::controller(OpdMasterController::class)->prefix('master')->name('master.')->group(function () {
            // Halaman utama master data dengan sistem tab
            Route::get('/', 'index')->name('index');
            
            // CRUD Lokasi
            Route::post('/location', 'locationStore')->name('locationStore');
            Route::put('/location/{location}', 'locationUpdate')->name('locationUpdate');
            Route::delete('/location/{location}', 'locationDestroy')->name('locationDestroy');
            
            // AJAX Endpoints untuk Lokasi
            Route::get('/location/{location}', 'getLocation')->name('getLocation');
            Route::get('/location-stats', 'getLocationStats')->name('locationStats');
            Route::get('/search-locations', 'searchLocations')->name('searchLocations');
            
            // Pindahkan aset ke lokasi lain
            Route::post('/move-asset/{asset}', 'moveAsset')->name('moveAsset');
        });
        
        // Transaction Management
        Route::controller(OpdTransactionController::class)->prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            
            // Store Routes by Type
            Route::post('/deletions', 'storeDeletion')->name('storeDeletion');
            Route::post('/mutations', 'storeMutation')->name('storeMutation');
            Route::post('/maintenances', 'storeMaintenance')->name('storeMaintenance');
            
            // Show/Cancel by Type
            Route::get('/{type}/{id}', 'show')->where('type', 'deletion|mutation|maintenance')->name('show');
            Route::post('/{type}/{id}/cancel', 'cancel')->where('type', 'deletion|mutation|maintenance')->name('cancel');
            
            // Maintenance specific
            Route::put('/maintenances/{maintenance}/status', 'updateMaintenanceStatus')->name('updateMaintenanceStatus');
            
            // AJAX Routes
            Route::post('/mutations/{mutation}/accept', 'acceptMutation')->name('acceptMutation');
            Route::get('/statistics', 'getStatistics')->name('statistics');
        });
    });
});

// ==================== PUBLIC ROUTES ====================
Route::get('/welcome', function () {
    return view('auth.login');
})->middleware('guest')->name('login');