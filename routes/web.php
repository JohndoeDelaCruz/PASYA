<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('landing');
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
    Route::get('/recommendations', [AdminController::class, 'recommendations'])->name('recommendations');
    
    // Farmer management routes
    Route::get('/farmers/{id}', [AdminController::class, 'getFarmer'])->name('farmers.get');
    Route::put('/farmers/{id}', [AdminController::class, 'updateFarmer'])->name('farmers.update');
    Route::post('/farmers/{id}/toggle-status', [AdminController::class, 'toggleFarmerStatus'])->name('farmers.toggle-status');
    Route::delete('/farmers/{id}', [AdminController::class, 'deleteFarmer'])->name('farmers.delete');
    Route::post('/farmers/batch-import', [AdminController::class, 'batchImportFarmers'])->name('farmers.batch-import');
    
    // Crop production management routes
    Route::resource('crops', App\Http\Controllers\CropController::class);
    
    // Crop Management Routes (CRUD for crop types and municipalities)
    Route::get('/crop-management', [App\Http\Controllers\CropManagementController::class, 'index'])->name('crop-management.index');
    
    // Combined Route (add both crop type and municipality)
    Route::post('/crop-management/combined', [App\Http\Controllers\CropManagementController::class, 'storeCombined'])->name('crop-management.combined.store');
    
    // Crop Type CRUD Routes
    Route::post('/crop-management/crop-types', [App\Http\Controllers\CropManagementController::class, 'storeCropType'])->name('crop-management.crop-types.store');
    Route::put('/crop-management/crop-types/{name}', [App\Http\Controllers\CropManagementController::class, 'updateCropType'])->name('crop-management.crop-types.update');
    Route::delete('/crop-management/crop-types/{name}', [App\Http\Controllers\CropManagementController::class, 'deleteCropType'])->name('crop-management.crop-types.delete');
    
    // Municipality CRUD Routes
    Route::post('/crop-management/municipalities', [App\Http\Controllers\CropManagementController::class, 'storeMunicipality'])->name('crop-management.municipalities.store');
    Route::put('/crop-management/municipalities/{name}', [App\Http\Controllers\CropManagementController::class, 'updateMunicipality'])->name('crop-management.municipalities.update');
    Route::delete('/crop-management/municipalities/{name}', [App\Http\Controllers\CropManagementController::class, 'deleteMunicipality'])->name('crop-management.municipalities.delete');
    
    // Crop import/export routes
    Route::get('/crops/import-export', [App\Http\Controllers\CropController::class, 'importExport'])->name('crops.import-export');
    Route::post('/crops/import', [App\Http\Controllers\CropController::class, 'import'])->name('crops.import');
    Route::get('/crops/import-progress', [App\Http\Controllers\CropController::class, 'importProgress'])->name('crops.import-progress');
    Route::get('/crops/export', [App\Http\Controllers\CropController::class, 'export'])->name('crops.export');
    Route::get('/crops/template', [App\Http\Controllers\CropController::class, 'downloadTemplate'])->name('crops.template');
    Route::get('/crops/{crop}/export', [App\Http\Controllers\CropController::class, 'exportSingle'])->name('crops.export-single');
    Route::post('/crops/delete-multiple', [App\Http\Controllers\CropController::class, 'deleteMultiple'])->name('crops.delete-multiple');
    Route::post('/crops/delete-all', [App\Http\Controllers\CropController::class, 'deleteAll'])->name('crops.delete-all');
    Route::post('/crops/delete-page', [App\Http\Controllers\CropController::class, 'deletePage'])->name('crops.delete-page');
    Route::post('/crops/test-import', [App\Http\Controllers\CropController::class, 'testImport'])->name('crops.test-import');
    Route::get('/test-upload', function() { return view('test_upload'); });
    Route::get('/test-auth', function() { 
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
            'guard' => Auth::getDefaultDriver()
        ]); 
    });
    
    // Test median imputation route
    Route::get('/test-median-imputation', function() {
        try {
            $service = new \App\Services\CropImportExportService();
            
            // Get reflection to access private methods for testing
            $reflection = new ReflectionClass($service);
            $calculateMedianMethod = $reflection->getMethod('calculateMedianValues');
            $calculateMedianMethod->setAccessible(true);
            
            $medianValues = $calculateMedianMethod->invoke($service);
            
            // Get current crop count and some sample data for context
            $totalCrops = \App\Models\Crop::count();
            $sampleCrops = \App\Models\Crop::where('area_planted', '>', 0)
                ->where('area_harvested', '>', 0)
                ->where('production_mt', '>', 0)
                ->where('productivity_mt_ha', '>', 0)
                ->take(5)
                ->get(['area_planted', 'area_harvested', 'production_mt', 'productivity_mt_ha']);
            
            return response()->json([
                'message' => 'Median Imputation System Ready',
                'total_crops_in_database' => $totalCrops,
                'calculated_median_values' => $medianValues,
                'sample_existing_data' => $sampleCrops,
                'explanation' => [
                    'area_planted_median' => 'Median area planted from all existing crops (hectares)',
                    'area_harvested_median' => 'Median area harvested from all existing crops (hectares)', 
                    'production_median' => 'Median production from all existing crops (metric tons)',
                    'productivity_median' => 'Median productivity from all existing crops (mt/ha)',
                    'usage' => 'These values will replace missing/null values in CSV imports'
                ],
                'benefits' => [
                    'statistical_accuracy' => 'Maintains data distribution better than zeros or averages',
                    'outlier_resistance' => 'Not affected by extremely high or low values',
                    'realistic_defaults' => 'Represents typical agricultural values in your region'
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    });
    
    // Debug import route
    Route::get('/debug-import', function() {
        $filePath = 'C:\Users\Admin\Desktop\PASYA\test_crop_data.csv';
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Test file not found at: ' . $filePath]);
        }
        
        try {
            $csvContent = file_get_contents($filePath);
            $lines = explode("\n", trim($csvContent));
            $headers = str_getcsv($lines[0]);
            
            $data = [];
            for ($i = 1; $i < count($lines); $i++) {
                if (trim($lines[$i])) {
                    $row = str_getcsv($lines[$i]);
                    $data[] = array_combine($headers, $row);
                }
            }
            
            return response()->json([
                'file_exists' => true,
                'file_size' => filesize($filePath),
                'line_count' => count($lines),
                'headers' => $headers,
                'sample_data' => array_slice($data, 0, 3),
                'total_records' => count($data)
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    });
});
