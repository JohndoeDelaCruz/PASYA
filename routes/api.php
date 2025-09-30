<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CropController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes for Crop production management (RESTful CRUD)
Route::prefix('crops')->group(function () {
    // GET /api/crops - List all crops
    Route::get('/', [CropController::class, 'apiIndex']);
    
    // POST /api/crops - Create a new crop
    Route::post('/', [CropController::class, 'apiStore']);
    
    // GET /api/crops/{id} - Get a specific crop
    Route::get('/{crop}', [CropController::class, 'apiShow']);
    
    // PUT /api/crops/{id} - Update a specific crop 
    Route::put('/{crop}', [CropController::class, 'apiUpdate']);
    
    // DELETE /api/crops/{id} - Delete a specific crop
    Route::delete('/{crop}', [CropController::class, 'apiDestroy']);
    
    // POST /api/crops/batch-delete - Delete multiple crops
    Route::post('/batch-delete', [CropController::class, 'apiBatchDelete']);
    
    // GET /api/crops/search/{term} - Search crops
    Route::get('/search/{term}', [CropController::class, 'apiSearch']);
    
    // GET /api/crops/filter/{municipality}/{crop?} - Filter crops by municipality and optionally by crop
    Route::get('/filter/{municipality}/{crop?}', [CropController::class, 'apiFilter']);
});

// Get reference data for dropdowns
Route::prefix('reference')->group(function () {
    // GET /api/reference/municipalities - Get list of Benguet municipalities
    Route::get('/municipalities', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'Atok', 'Bakun', 'Bokod', 'Buguias', 'Itogon', 
                'Kabayan', 'Kapangan', 'Kibungan', 'La Trinidad', 
                'Mankayan', 'Sablan', 'Tuba', 'Tublay'
            ]
        ]);
    });
    
    // GET /api/reference/highland-crops - Get list of highland crops
    Route::get('/highland-crops', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'Cabbage', 'Carrots', 'Broccoli', 'Potato', 'Lettuce',
                'Cauliflower', 'Bell Pepper', 'Onion', 'Tomato', 'Chinese Cabbage (Pechay)'
            ]
        ]);
    });
    
    // GET /api/reference/farm-types - Get list of farm types
    Route::get('/farm-types', function () {
        return response()->json([
            'success' => true,
            'data' => ['irrigated', 'rainfed', 'upland', 'lowland']
        ]);
    });
});