<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Register route (placeholder)
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Farmer Dashboard Routes
Route::middleware(['farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', [FarmerController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [FarmerController::class, 'profile'])->name('profile');
    Route::get('/calendar', [FarmerController::class, 'calendar'])->name('calendar');
    Route::get('/harvest-history', [FarmerController::class, 'harvestHistory'])->name('harvest-history');
    Route::get('/pricelist-watch', [FarmerController::class, 'pricelistWatch'])->name('pricelist-watch');
});

// Admin Dashboard Routes
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/data-analytics', [AdminController::class, 'dataAnalytics'])->name('data-analytics');
    Route::get('/crop-trends', [AdminController::class, 'cropTrends'])->name('crop-trends');
    Route::match(['get', 'post'], '/create-account', [AdminController::class, 'createAccount'])->name('create-account');
    Route::match(['get', 'post'], '/upload-data', [AdminController::class, 'uploadData'])->name('upload-data');
    Route::get('/recommendations', [AdminController::class, 'recommendations'])->name('recommendations');
    
    // Farmer management routes
    Route::get('/farmers/{id}', [AdminController::class, 'getFarmer'])->name('farmers.get');
    Route::put('/farmers/{id}', [AdminController::class, 'updateFarmer'])->name('farmers.update');
    Route::post('/farmers/{id}/toggle-status', [AdminController::class, 'toggleFarmerStatus'])->name('farmers.toggle-status');
    Route::delete('/farmers/{id}', [AdminController::class, 'deleteFarmer'])->name('farmers.delete');
    Route::post('/farmers/batch-import', [AdminController::class, 'batchImportFarmers'])->name('farmers.batch-import');
    
    // Crop management routes
    Route::resource('crops', App\Http\Controllers\CropController::class);
    
    // Crop import/export routes
    Route::get('/crops/import-export', [App\Http\Controllers\CropController::class, 'importExport'])->name('crops.import-export');
    Route::post('/crops/import', [App\Http\Controllers\CropController::class, 'import'])->name('crops.import');
    Route::get('/crops/export', [App\Http\Controllers\CropController::class, 'export'])->name('crops.export');
    Route::get('/crops/template', [App\Http\Controllers\CropController::class, 'downloadTemplate'])->name('crops.template');
    Route::get('/crops/{crop}/export', [App\Http\Controllers\CropController::class, 'exportSingle'])->name('crops.export-single');
    Route::post('/crops/delete-multiple', [App\Http\Controllers\CropController::class, 'deleteMultiple'])->name('crops.delete-multiple');
    Route::post('/crops/test-import', [App\Http\Controllers\CropController::class, 'testImport'])->name('crops.test-import');
    Route::get('/test-upload', function() { return view('test_upload'); });
    Route::get('/test-auth', function() { 
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
            'guard' => Auth::getDefaultDriver()
        ]); 
    });
});
