<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\ProposalController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AuditReportController;
use Illuminate\Support\Facades\Route;

// Group dengan middleware admin
Route::middleware(['auth', 'admin.utama'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Routes
    Route::controller(DashboardController::class)->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/statistics', 'statistics')->name('statistics');
        Route::get('/asset-distribution', 'assetDistribution')->name('asset-distribution');
        Route::get('/asset-condition', 'assetCondition')->name('asset-condition');
    });
    
    // Asset Routes
    Route::controller(AssetController::class)->prefix('assets')->name('assets.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{asset}', 'show')->name('show');
        Route::post('/{asset}/verify-document', 'verifyDocument')->name('verify-document');
        Route::post('/{asset}/validate', 'validateAsset')->name('validate');
        Route::get('/bulk-actions', 'bulkActions')->name('bulk-actions');
        Route::post('/bulk-actions/process', 'processBulkActions')->name('process-bulk-actions');
        Route::get('/export', 'export')->name('export');
    });
    
    // Unified Proposal Routes (Mutations & Deletions)
    Route::controller(ProposalController::class)->prefix('proposals')->name('proposals.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/bulk-approval', 'bulkApproval')->name('bulk-approval');
        Route::post('/bulk-approval/process', 'processBulkApproval')->name('process-bulk-approval');
        
        // Type-specific routes (mutations/deletions)
        Route::prefix('{type}')->where(['type' => 'mutation|deletion'])->group(function () {
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}/verify', 'verify')->name('verify');
            Route::post('/{id}/approve', 'approve')->name('approve');
            Route::post('/{id}/complete', 'complete')->name('complete');
            Route::post('/{id}/reject', 'reject')->name('reject');
            Route::post('/{id}/cancel', 'cancel')->name('cancel');
        });
    });
    
    // Report Routes
    Route::controller(ReportController::class)->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/generate/{type}', 'generate')->name('generate');
        Route::post('/process', 'process')->name('process');
    });
    
    // Settings Routes (Unified CRUD)
    Route::controller(SettingController::class)->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', 'index')->name('index');
        
        // Unified CRUD routes
        Route::get('/{section}/{action?}/{id?}', 'manage')->name('manage')
            ->where('section', 'categories|opd-units|users|system')
            ->where('action', 'index|create|edit|view|logs');
        
        Route::post('/{section}/store/{id?}', 'storeOrUpdate')->name('store');
        Route::delete('/{section}/{id}', 'destroy')->name('destroy');
        Route::post('/backup', 'backup')->name('backup');
    });
    
    // Audit Report Routes (Tetap terpisah karena spesifik)
    Route::controller(AuditReportController::class)->prefix('audits')->name('audits.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{audit}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::post('/{audit}/update-status', 'updateStatus')->name('update-status');
        Route::get('/{audit}/download-file', 'downloadFile')->name('download-file');
    });
    
    // Home redirect
    Route::get('/', function () {
        return redirect()->route('admin.dashboard.index');
    })->name('home');
});