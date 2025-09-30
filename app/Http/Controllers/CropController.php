<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Farmer;
use App\Services\CropImportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CropController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Admin can see all crops from all farmers
        $perPage = $request->get('per_page', 15); // Default to 15 if not specified
        
        // Validate per_page parameter
        if (!in_array($perPage, [10, 15, 25, 50])) {
            $perPage = 15;
        }
        
        // Build query with filters
        $query = Crop::with('farmer');
        
        // Apply comprehensive search filter across all relevant fields
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                // Search in text fields
                $q->where('crop_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('municipality', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('farm_type', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('status', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('crop_category', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('production_month', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('production_farm_type', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('variety', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                
                // Search in numeric fields (if search term is numeric)
                if (is_numeric($searchTerm)) {
                    $numericValue = floatval($searchTerm);
                    $q->orWhere('year', '=', $numericValue)
                      ->orWhere('area_planted', '=', $numericValue)
                      ->orWhere('area_harvested', '=', $numericValue)
                      ->orWhere('production_mt', '=', $numericValue)
                      ->orWhere('productivity_mt_ha', '=', $numericValue)
                      ->orWhere('crop_days_to_maturity', '=', $numericValue)
                      ->orWhere('area_hectares', '=', $numericValue)
                      ->orWhere('expected_yield_kg', '=', $numericValue)
                      ->orWhere('actual_yield_kg', '=', $numericValue);
                } else {
                    // For non-numeric searches, also search year as string
                    $q->orWhere('year', 'LIKE', "%{$searchTerm}%");
                }
                
                // Search in date fields (if search term looks like a date)
                if (preg_match('/\d{4}-\d{2}-\d{2}/', $searchTerm)) {
                    $q->orWhere('planting_date', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('expected_harvest_date', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('actual_harvest_date', 'LIKE', "%{$searchTerm}%");
                }
                
                // Search in related farmer data if farmer relationship exists
                $q->orWhereHas('farmer', function($farmerQuery) use ($searchTerm) {
                    $farmerQuery->where('farmerName', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('farmerLocation', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('farmerAddress', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('farmerContact', 'LIKE', "%{$searchTerm}%");
                });
            });
        }
        
        // Apply comprehensive filters that search through all datasets
        
        // Comprehensive Municipality filter - searches through all location-related fields
        if ($request->filled('municipality')) {
            $municipalityFilter = $request->get('municipality');
            $query->where(function($q) use ($municipalityFilter) {
                $q->where('municipality', 'LIKE', "%{$municipalityFilter}%")
                  ->orWhere('farmerLocation', 'LIKE', "%{$municipalityFilter}%")
                  ->orWhere('farmerAddress', 'LIKE', "%{$municipalityFilter}%");
                
                // Also search in related farmer data
                $q->orWhereHas('farmer', function($farmerQuery) use ($municipalityFilter) {
                    $farmerQuery->where('farmerLocation', 'LIKE', "%{$municipalityFilter}%")
                               ->orWhere('farmerAddress', 'LIKE', "%{$municipalityFilter}%")
                               ->orWhere('municipality', 'LIKE', "%{$municipalityFilter}%");
                });
            });
        }
        
        // Comprehensive Crop filter - searches through all crop-related fields  
        if ($request->filled('crop')) {
            $cropFilter = $request->get('crop');
            $query->where(function($q) use ($cropFilter) {
                $q->where('crop_name', 'LIKE', "%{$cropFilter}%")
                  ->orWhere('name', 'LIKE', "%{$cropFilter}%")
                  ->orWhere('variety', 'LIKE', "%{$cropFilter}%")
                  ->orWhere('crop_category', 'LIKE', "%{$cropFilter}%")
                  ->orWhere('description', 'LIKE', "%{$cropFilter}%");
            });
        }
        
        // Farm type filter
        if ($request->filled('farm_type')) {
            $query->where('farm_type', $request->get('farm_type'));
        }
        
        // Year filter
        if ($request->filled('year')) {
            $query->where('year', $request->get('year'));
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        
        // Crop category filter
        if ($request->filled('crop_category')) {
            $query->where('crop_category', $request->get('crop_category'));
        }
        
        // Production month filter
        if ($request->filled('production_month')) {
            $query->where('production_month', $request->get('production_month'));
        }
        
        // Area planted range filter
        if ($request->filled('min_area_planted')) {
            $query->where('area_planted', '>=', $request->get('min_area_planted'));
        }
        if ($request->filled('max_area_planted')) {
            $query->where('area_planted', '<=', $request->get('max_area_planted'));
        }
        
        // Production range filter
        if ($request->filled('min_production')) {
            $query->where('production_mt', '>=', $request->get('min_production'));
        }
        if ($request->filled('max_production')) {
            $query->where('production_mt', '<=', $request->get('max_production'));
        }
        
        // Productivity range filter
        if ($request->filled('min_productivity')) {
            $query->where('productivity_mt_ha', '>=', $request->get('min_productivity'));
        }
        if ($request->filled('max_productivity')) {
            $query->where('productivity_mt_ha', '<=', $request->get('max_productivity'));
        }
        
        // Date range filters
        if ($request->filled('date_from')) {
            $query->where('planting_date', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('planting_date', '<=', $request->get('date_to'));
        }
        
        // Apply sorting with multiple options
        if ($request->filled('sort')) {
            $sortDirection = $request->get('sort') === 'desc' ? 'desc' : 'asc';
            $sortBy = $request->get('sort_by', 'crop_name');
            
            // Validate sort field to prevent SQL injection
            $allowedSortFields = [
                'crop_name', 'municipality', 'farm_type', 'year', 'area_planted', 
                'area_harvested', 'production_mt', 'productivity_mt_ha', 'created_at'
            ];
            
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->orderBy('crop_name', $sortDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $crops = $query->paginate($perPage);
        
        // If current page is empty but there are crops, redirect to first page
        if ($crops->isEmpty() && $crops->total() > 0 && $request->get('page', 1) > 1) {
            return redirect()->route('admin.crops.index', array_merge($request->query(), ['page' => 1]));
        }
        
        // Append current query parameters to pagination links
        $crops->appends($request->query());
        
        // Get comprehensive filter data
        $allMunicipalities = $this->getMunicipalities();
        $allCropNames = $this->getHighlandCrops();
        $allCropCategories = $this->getCropCategories();
        $highlandCropsData = $this->getHighlandCropsData();
        $allProductionMonths = $this->getProductionMonths();
        $allProductionFarmTypes = $this->getProductionFarmTypes();
        
        // Additional filter options
        $allFarmTypes = $this->getFarmTypes();
        $allYears = $this->getAvailableYears();
        $allStatuses = $this->getAvailableStatuses();
        $productionRanges = $this->getProductionRanges();
        $areaRanges = $this->getAreaRanges();
        $productivityRanges = $this->getProductivityRanges();
        
        // Debug logging
        $totalCrops = Crop::count();
        $sampleCrops = Crop::select(['id', 'municipality', 'crop_name', 'name'])->limit(5)->get();
        
        return view('admin.crops.index', compact(
            'crops', 'allMunicipalities', 'allCropNames', 'allCropCategories', 
            'highlandCropsData', 'allProductionMonths', 'allProductionFarmTypes',
            'allFarmTypes', 'allYears', 'allStatuses', 'productionRanges', 
            'areaRanges', 'productivityRanges'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all farmers for the dropdown
        $farmers = Farmer::active()->orderBy('farmerName')->get();
        
        return view('admin.crops.create', compact('farmers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Store method called', [
                'is_ajax' => $request->ajax(),
                'expects_json' => $request->expectsJson(),
                'content_type' => $request->header('Content-Type'),
                'has_municipality' => $request->has('municipality'),
                'has_crop_name' => $request->has('crop_name')
            ]);
            
            // Get standardized data
            $municipalities = $this->getMunicipalities();
            $highlandCrops = $this->getHighlandCrops();
            
            // Check if this is the new modal format (agricultural statistics data) or old format
            if ($request->has('municipality') || $request->has('crop_name')) {
                // New modal format - agricultural statistics data
                $cropCategories = $this->getCropCategories();
                $validated = $request->validate([
                    'municipality' => ['required', 'string', Rule::in($municipalities->toArray())],
                    'farm_type' => 'required|string|in:irrigated,rainfed,upland,lowland',
                    'year' => 'required|integer|min:2000|max:2030',
                    'crop_name' => ['required', 'string', Rule::in($highlandCrops->toArray())],
                    'area_planted' => 'required|numeric|min:0.01|max:99999.99',
                    'area_harvested' => 'required|numeric|min:0.01|max:99999.99|lte:area_planted',
                    'production' => 'required|numeric|min:0.01|max:99999999.99',
                    'productivity' => 'nullable|numeric|min:0|max:99999.99',
                    // New suggested fields
                    'cropID' => 'nullable|string|max:50|unique:crops,cropID',
                    'cropCategory' => ['nullable', 'string', Rule::in($cropCategories->toArray())],
                    'cropDaysToMaturity' => 'nullable|integer|min:1|max:365',
                    'productionMonth' => 'nullable|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
                    'productionFarmType' => 'nullable|string|in:Irrigated,Rainfed',
                ], [
                    'area_harvested.lte' => 'Area harvested cannot be greater than area planted.',
                ]);

                // Auto-populate category and days to maturity if not provided
                $highlandCropsData = $this->getHighlandCropsData();
                $cropInfo = $highlandCropsData->get($validated['crop_name'], []);
                
                // Map form field names to database field names
                $cropData = [
                    'municipality' => $validated['municipality'],
                    'farm_type' => $validated['farm_type'],
                    'year' => $validated['year'],
                    'crop_name' => $validated['crop_name'],
                    'area_planted' => $validated['area_planted'],
                    'area_harvested' => $validated['area_harvested'],
                    'production_mt' => $validated['production'],
                    'productivity_mt_ha' => $validated['productivity'],
                    'status' => 'planted', // Default status
                    // New suggested fields
                    'cropID' => $validated['cropID'],
                    'cropCategory' => $validated['cropCategory'] ?? $cropInfo['category'] ?? null,
                    'cropDaysToMaturity' => $validated['cropDaysToMaturity'] ?? $cropInfo['days_to_maturity'] ?? null,
                    'productionMonth' => $validated['productionMonth'],
                    'productionFarmType' => $validated['productionFarmType'],
                ];

                $crop = Crop::create($cropData);

                // Return JSON response for AJAX requests
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Crop data added successfully!',
                        'crop' => $crop
                    ]);
                }

                return redirect()->route('admin.crops.index')->with('success', 'Crop data added successfully!');
            } else {
                // Original format - individual farmer crop tracking
                $validated = $request->validate([
                    'farmer_id' => 'required|exists:tblFarmers,farmerID',
                    'name' => 'required|string|max:255',
                    'variety' => 'nullable|string|max:255',
                    'planting_date' => 'required|date',
                    'expected_harvest_date' => 'nullable|date|after:planting_date',
                    'area_hectares' => 'required|numeric|min:0.01|max:9999.99',
                    'description' => 'nullable|string|max:1000',
                    'expected_yield_kg' => 'nullable|numeric|min:0|max:99999999.99',
                ]);

                $crop = Crop::create($validated);

                // Return JSON response for AJAX requests
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Crop added successfully!',
                        'crop' => $crop
                    ]);
                }

                return redirect()->route('admin.crops.index')->with('success', 'Crop added successfully!');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return JSON response for AJAX requests with validation errors
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            // Return JSON response for AJAX requests with general errors
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Crop $crop, Request $request)
    {
        // Load the farmer relationship
        $crop->load('farmer');
        
        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($crop);
        }
        
        return view('admin.crops.show', compact('crop'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Crop $crop)
    {
        // Get all farmers for the dropdown
        $farmers = Farmer::active()->orderBy('farmerName')->get();
        
        return view('admin.crops.edit', compact('crop', 'farmers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Crop $crop)
    {
        try {
            // Get standardized data
            $municipalities = $this->getMunicipalities();
            $highlandCrops = $this->getHighlandCrops();
            
            // Check if this is the new modal format (agricultural statistics data) or old format
            if ($request->has('municipality') || $request->has('crop_name')) {
                // New modal format - agricultural statistics data
                $cropCategories = $this->getCropCategories();
                $validated = $request->validate([
                    'municipality' => ['required', 'string', Rule::in($municipalities->toArray())],
                    'farm_type' => 'required|string|in:irrigated,rainfed,upland,lowland',
                    'year' => 'required|integer|min:2000|max:2030',
                    'crop_name' => ['required', 'string', Rule::in($highlandCrops->toArray())],
                    'area_planted' => 'required|numeric|min:0.01|max:99999.99',
                    'area_harvested' => 'required|numeric|min:0.01|max:99999.99|lte:area_planted',
                    'production' => 'required|numeric|min:0.01|max:99999999.99',
                    'productivity' => 'nullable|numeric|min:0|max:99999.99',
                    // New suggested fields
                    'cropID' => 'nullable|string|max:50|unique:crops,cropID,' . $crop->id,
                    'cropCategory' => ['nullable', 'string', Rule::in($cropCategories->toArray())],
                    'cropDaysToMaturity' => 'nullable|integer|min:1|max:365',
                    'productionMonth' => 'nullable|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
                    'productionFarmType' => 'nullable|string|in:Irrigated,Rainfed',
                ], [
                    'area_harvested.lte' => 'Area harvested cannot be greater than area planted.',
                ]);

                // Auto-populate category and days to maturity if not provided
                $highlandCropsData = $this->getHighlandCropsData();
                $cropInfo = $highlandCropsData->get($validated['crop_name'], []);

                // Map form field names to database field names
                $cropData = [
                    'municipality' => $validated['municipality'],
                    'farm_type' => $validated['farm_type'],
                    'year' => $validated['year'],
                    'crop_name' => $validated['crop_name'],
                    'area_planted' => $validated['area_planted'],
                    'area_harvested' => $validated['area_harvested'],
                    'production_mt' => $validated['production'],
                    'productivity_mt_ha' => $validated['productivity'],
                    // New suggested fields
                    'cropID' => $validated['cropID'],
                    'cropCategory' => $validated['cropCategory'] ?? $cropInfo['category'] ?? null,
                    'cropDaysToMaturity' => $validated['cropDaysToMaturity'] ?? $cropInfo['days_to_maturity'] ?? null,
                    'productionMonth' => $validated['productionMonth'],
                    'productionFarmType' => $validated['productionFarmType'],
                ];

                $crop->update($cropData);

                // Return JSON response for AJAX requests
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Crop updated successfully!',
                        'crop' => $crop->fresh()
                    ]);
                }

                return redirect()->route('admin.crops.index')->with('success', 'Crop updated successfully!');
            } else {
                // Old format - farmer crop data
                $validated = $request->validate([
                    'farmer_id' => 'required|exists:tblFarmers,farmerID',
                    'name' => 'required|string|max:255',
                    'variety' => 'nullable|string|max:255',
                    'planting_date' => 'required|date',
                    'expected_harvest_date' => 'nullable|date|after:planting_date',
                    'area_hectares' => 'required|numeric|min:0.01|max:9999.99',
                    'description' => 'nullable|string|max:1000',
                    'expected_yield_kg' => 'nullable|numeric|min:0|max:99999999.99',
                ]);

                $crop->update($validated);

                return redirect()->route('admin.crops.index')->with('success', 'Crop updated successfully!');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Crop $crop)
    {
        try {
            $crop->delete(); // Soft delete (archive)
            
            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Crop archived successfully!'
                ]);
            }
            
            return redirect()->route('admin.crops.index')->with('success', 'Crop archived successfully!');
        } catch (\Exception $e) {
            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the crop: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.crops.index')->with('error', 'Failed to delete crop. Please try again.');
        }
    }

    /**
     * Show import/export page
     */
    public function importExport()
    {
        return view('admin.crops.import-export');
    }

    /**
     * Import crops from CSV
     */
    public function import(Request $request, CropImportExportService $service)
    {
        \Log::info('Import method reached - START');
        
        // Set higher limits for large file processing
        ini_set('max_execution_time', 600); // 10 minutes
        ini_set('memory_limit', '512M'); // Increase memory limit
        
        try {
            \Log::info('Import method called', [
                'is_ajax' => $request->ajax(),
                'expects_json' => $request->expectsJson(),
                'has_file' => $request->hasFile('csv_file'),
                'file_name' => $request->file('csv_file') ? $request->file('csv_file')->getClientOriginalName() : 'no file',
                'file_extension' => $request->file('csv_file') ? $request->file('csv_file')->getClientOriginalExtension() : 'no extension',
                'file_size' => $request->file('csv_file') ? $request->file('csv_file')->getSize() : 0,
                'all_files' => array_keys($request->allFiles()),
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type')
            ]);
            
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:102400' // 100MB limit for large files
            ]);

            \Log::info('Validation passed');

            $file = $request->file('csv_file');
            $extension = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();
            $isLargeFile = $fileSize > 5 * 1024 * 1024; // 5MB threshold
            
            \Log::info('Processing file', [
                'extension' => $extension,
                'file_size' => $fileSize,
                'is_large_file' => $isLargeFile,
                'will_use_excel' => in_array($extension, ['xlsx', 'xls'])
            ]);
            
            // For large files, provide additional feedback
            if ($isLargeFile && ($request->expectsJson() || $request->ajax())) {
                // Send immediate response for large files
                return response()->json([
                    'success' => true,
                    'message' => 'Large file detected. Processing in the background. This may take several minutes...',
                    'processing' => true
                ]);
            }
            
            // Handle different file types
            if (in_array($extension, ['xlsx', 'xls'])) {
                $results = $service->importFromExcel($file);
            } else {
                $results = $service->importFromCsv($file);
            }

            \Log::info('Import results', [
                'success_count' => $results['success'],
                'error_count' => $results['errors'],
                'messages' => $results['messages'] ?? []
            ]);
            
            $message = "Import completed: {$results['success']} successful, {$results['errors']} errors.";
            
            if (!empty($results['messages'])) {
                $message .= "\n\nDetails:\n" . implode("\n", $results['messages']);
            }

            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                if ($results['errors'] > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            if ($results['errors'] > 0) {
                return redirect()->route('admin.crops.import-export')
                    ->with('warning', $message);
            }

            return redirect()->route('admin.crops.import-export')
                ->with('success', $message);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Test AJAX endpoint
     */


    /**
     * Delete multiple crops
     */
    public function deleteMultiple(Request $request)
    {
        try {
            \Log::info('DeleteMultiple method called', [
                'is_ajax' => $request->ajax(),
                'expects_json' => $request->expectsJson(),
                'content_type' => $request->header('Content-Type'),
                'request_data' => $request->all()
            ]);
            
            $validated = $request->validate([
                'crop_ids' => 'required|array',
                'crop_ids.*' => 'integer|min:1'
            ]);

            $cropIds = $validated['crop_ids'];
            
            // Log the crop IDs being archived for verification
            \Log::info('Attempting to archive crops with IDs: ' . implode(', ', $cropIds));
            
            // Count before archiving (non-archived crops)
            $countBefore = Crop::count();
            
            // Only archive crops that actually exist and are not already archived
            $existingCropIds = Crop::whereIn('id', $cropIds)->pluck('id')->toArray();
            
            if (empty($existingCropIds)) {
                \Log::warning('No valid crops found to archive from IDs: ' . implode(', ', $cropIds));
                
                // Always return JSON for delete-multiple endpoint
                return response()->json([
                    'success' => false,
                    'message' => 'Selected items are already archived or do not exist.',
                    'debug' => [
                        'requested_ids' => $cropIds,
                        'existing_ids' => $existingCropIds
                    ]
                ], 200); // Change to 200 to avoid potential error page renders
            }
            
            // Force delete (permanent deletion) to ensure items don't reappear
            $cropsToDelete = Crop::whereIn('id', $existingCropIds)->get();
            $archivedCount = 0;
            $failedDeletes = [];
            
            foreach ($cropsToDelete as $crop) {
                try {
                    $crop->forceDelete(); // Permanent deletion
                    $archivedCount++;
                    \Log::info("Successfully deleted crop ID: {$crop->id}");
                } catch (\Exception $deleteError) {
                    $failedDeletes[] = $crop->id;
                    \Log::error("Failed to delete crop ID {$crop->id}: " . $deleteError->getMessage());
                }
            }
            
            // Count after deletion
            $countAfter = Crop::count();
            
            // If some deletions failed, include that in the response
            if (!empty($failedDeletes)) {
                \Log::warning("Some crops could not be deleted: " . implode(', ', $failedDeletes));
            }
            
            \Log::info("Crop count before archiving: {$countBefore}, after archiving: {$countAfter}, archived: {$archivedCount}");

                // Always return JSON response for delete-multiple endpoint
                $message = "Successfully deleted {$archivedCount} item(s)";
                if (!empty($failedDeletes)) {
                    $message .= " (" . count($failedDeletes) . " items could not be deleted)";
                }
                
                return response()->json([
                    'success' => $archivedCount > 0,
                    'message' => $message,
                    'debug' => [
                        'crop_ids' => $existingCropIds,
                        'deleted_count' => $archivedCount,
                        'failed_deletes' => $failedDeletes,
                        'before_count' => $countBefore,
                        'after_count' => $countAfter
                    ]
                ]);        } catch (\Exception $e) {
            \Log::error('Error archiving crops: ' . $e->getMessage());
            // Always return JSON for delete-multiple endpoint
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test import endpoint
     */
    public function testImport(Request $request)
    {
        \Log::info('Test import called', [
            'has_file' => $request->hasFile('csv_file'),
            'all_files' => array_keys($request->allFiles()),
            'method' => $request->method(),
            'headers' => $request->headers->all()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Test import endpoint reached successfully',
            'has_file' => $request->hasFile('csv_file'),
            'files' => array_keys($request->allFiles())
        ]);
    }

    /**
     * Download import template
     */
    public function downloadTemplate(CropImportExportService $service)
    {
        $csvContent = $service->getCsvTemplate();
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="crop_import_template.csv"');
    }

    /**
     * Get Benguet municipalities
     */
    private function getMunicipalities()
    {
        return collect([
            'Atok', 'Bakun', 'Bokod', 'Buguias', 'Itogon', 
            'Kabayan', 'Kapangan', 'Kibungan', 'La Trinidad', 
            'Mankayan', 'Sablan', 'Tuba', 'Tublay'
        ]);
    }

    /**
     * Get highland crops
     */
    private function getHighlandCrops()
    {
        return collect([
            'Cabbage', 'Carrots', 'Broccoli', 'Potato', 'Lettuce',
            'Cauliflower', 'Bell Pepper', 'Onion', 'Tomato', 'Chinese Cabbage (Pechay)'
        ]);
    }

    /**
     * Get crop categories
     */
    private function getCropCategories()
    {
        return collect([
            'Leafy Vegetables',
            'Root Crops',
            'Cruciferous Vegetables',
            'Fruit Vegetables',
            'Tuber Crops'
        ]);
    }

    /**
     * Get highland crops with their default data including categories and days to maturity
     */
    private function getHighlandCropsData()
    {
        return collect([
            'Cabbage' => ['category' => 'Leafy Vegetables', 'days_to_maturity' => 70],
            'Carrots' => ['category' => 'Root Crops', 'days_to_maturity' => 75],
            'Broccoli' => ['category' => 'Cruciferous Vegetables', 'days_to_maturity' => 85],
            'Potato' => ['category' => 'Tuber Crops', 'days_to_maturity' => 90],
            'Lettuce' => ['category' => 'Leafy Vegetables', 'days_to_maturity' => 45],
            'Cauliflower' => ['category' => 'Cruciferous Vegetables', 'days_to_maturity' => 80],
            'Bell Pepper' => ['category' => 'Fruit Vegetables', 'days_to_maturity' => 75],
            'Onion' => ['category' => 'Root Crops', 'days_to_maturity' => 120],
            'Tomato' => ['category' => 'Fruit Vegetables', 'days_to_maturity' => 80],
            'Chinese Cabbage (Pechay)' => ['category' => 'Leafy Vegetables', 'days_to_maturity' => 50]
        ]);
    }

    // ======================
    // API METHODS FOR CRUD
    // ======================

    /**
     * API: Get all crops with pagination and filters
     */
    public function apiIndex(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        
        // Validate per_page parameter
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }
        
        // Build query with filters
        $query = Crop::with('farmer');
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('crop_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('municipality', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('year', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Apply municipality filter
        if ($request->filled('municipality')) {
            $query->where('municipality', $request->get('municipality'));
        }
        
        // Apply crop filter
        if ($request->filled('crop')) {
            $query->where('crop_name', $request->get('crop'));
        }
        
        // Apply sorting
        if ($request->filled('sort')) {
            $sortDirection = $request->get('sort') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('crop_name', $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $crops = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $crops->items(),
            'pagination' => [
                'current_page' => $crops->currentPage(),
                'per_page' => $crops->perPage(),
                'total' => $crops->total(),
                'last_page' => $crops->lastPage(),
                'from' => $crops->firstItem(),
                'to' => $crops->lastItem(),
            ],
            'reference_data' => [
                'municipalities' => $this->getMunicipalities(),
                'highland_crops' => $this->getHighlandCrops()
            ]
        ]);
    }

    /**
     * API: Create a new crop
     */
    public function apiStore(Request $request)
    {
        try {
            // Get standardized data
            $municipalities = $this->getMunicipalities();
            $highlandCrops = $this->getHighlandCrops();
            $cropCategories = $this->getCropCategories();
            
            $validated = $request->validate([
                'municipality' => ['required', 'string', Rule::in($municipalities->toArray())],
                'farm_type' => 'required|string|in:irrigated,rainfed,upland,lowland',
                'year' => 'required|integer|min:2000|max:2030',
                'crop_name' => ['required', 'string', Rule::in($highlandCrops->toArray())],
                'area_planted' => 'required|numeric|min:0.01|max:99999.99',
                'area_harvested' => 'required|numeric|min:0.01|max:99999.99|lte:area_planted',
                'production_mt' => 'required|numeric|min:0.01|max:99999999.99',
                'productivity_mt_ha' => 'nullable|numeric|min:0|max:99999.99',
                // New suggested fields
                'cropID' => 'nullable|string|max:50|unique:crops,cropID',
                'cropCategory' => ['nullable', 'string', Rule::in($cropCategories->toArray())],
                'cropDaysToMaturity' => 'nullable|integer|min:1|max:365',
                'productionMonth' => 'nullable|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
                'productionFarmType' => 'nullable|string|in:Irrigated,Rainfed',
            ], [
                'area_harvested.lte' => 'Area harvested cannot be greater than area planted.',
            ]);

            // Auto-populate category and days to maturity if not provided
            $highlandCropsData = $this->getHighlandCropsData();
            $cropInfo = $highlandCropsData->get($validated['crop_name'], []);
            
            $validated['cropCategory'] = $validated['cropCategory'] ?? $cropInfo['category'] ?? null;
            $validated['cropDaysToMaturity'] = $validated['cropDaysToMaturity'] ?? $cropInfo['days_to_maturity'] ?? null;

            $crop = Crop::create($validated);
            $crop->load('farmer');

            return response()->json([
                'success' => true,
                'message' => 'Crop created successfully!',
                'data' => $crop
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get a specific crop
     */
    public function apiShow(Crop $crop)
    {
        $crop->load('farmer');
        
        return response()->json([
            'success' => true,
            'data' => $crop
        ]);
    }

    /**
     * API: Update a specific crop
     */
    public function apiUpdate(Request $request, Crop $crop)
    {
        try {
            // Get standardized data
            $municipalities = $this->getMunicipalities();
            $highlandCrops = $this->getHighlandCrops();
            $cropCategories = $this->getCropCategories();
            
            $validated = $request->validate([
                'municipality' => ['required', 'string', Rule::in($municipalities->toArray())],
                'farm_type' => 'required|string|in:irrigated,rainfed,upland,lowland',
                'year' => 'required|integer|min:2000|max:2030',
                'crop_name' => ['required', 'string', Rule::in($highlandCrops->toArray())],
                'area_planted' => 'required|numeric|min:0.01|max:99999.99',
                'area_harvested' => 'required|numeric|min:0.01|max:99999.99|lte:area_planted',
                'production_mt' => 'required|numeric|min:0.01|max:99999999.99',
                'productivity_mt_ha' => 'nullable|numeric|min:0|max:99999.99',
                // New suggested fields
                'cropID' => 'nullable|string|max:50|unique:crops,cropID,' . $crop->id,
                'cropCategory' => ['nullable', 'string', Rule::in($cropCategories->toArray())],
                'cropDaysToMaturity' => 'nullable|integer|min:1|max:365',
                'productionMonth' => 'nullable|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
                'productionFarmType' => 'nullable|string|in:Irrigated,Rainfed',
            ], [
                'area_harvested.lte' => 'Area harvested cannot be greater than area planted.',
            ]);

            // Auto-populate category and days to maturity if not provided
            $highlandCropsData = $this->getHighlandCropsData();
            $cropInfo = $highlandCropsData->get($validated['crop_name'], []);
            
            $validated['cropCategory'] = $validated['cropCategory'] ?? $cropInfo['category'] ?? null;
            $validated['cropDaysToMaturity'] = $validated['cropDaysToMaturity'] ?? $cropInfo['days_to_maturity'] ?? null;

            $crop->update($validated);
            $crop->load('farmer');

            return response()->json([
                'success' => true,
                'message' => 'Crop updated successfully!',
                'data' => $crop
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Delete a specific crop
     */
    public function apiDestroy(Crop $crop)
    {
        try {
            $crop->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Crop deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the crop: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Delete multiple crops
     */
    public function apiBatchDelete(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'required|integer|exists:crops,id'
            ]);

            $deletedCount = Crop::whereIn('id', $validated['ids'])->delete();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} crop(s)",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Search crops
     */
    public function apiSearch(Request $request, $term)
    {
        $query = Crop::with('farmer')
            ->where(function($q) use ($term) {
                $q->where('crop_name', 'LIKE', "%{$term}%")
                  ->orWhere('municipality', 'LIKE', "%{$term}%")
                  ->orWhere('year', 'LIKE', "%{$term}%");
            })
            ->orderBy('created_at', 'desc');

        $crops = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $crops->items(),
            'pagination' => [
                'current_page' => $crops->currentPage(),
                'per_page' => $crops->perPage(),
                'total' => $crops->total(),
                'last_page' => $crops->lastPage(),
            ],
            'search_term' => $term
        ]);
    }

    /**
     * API: Filter crops by municipality and crop
     */
    public function apiFilter(Request $request, $municipality, $crop = null)
    {
        $query = Crop::with('farmer')
            ->where('municipality', $municipality);

        if ($crop) {
            $query->where('crop_name', $crop);
        }

        $crops = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $crops->items(),
            'pagination' => [
                'current_page' => $crops->currentPage(),
                'per_page' => $crops->perPage(),
                'total' => $crops->total(),
                'last_page' => $crops->lastPage(),
            ],
            'filters' => [
                'municipality' => $municipality,
                'crop' => $crop
            ]
        ]);
    }

    /**
     * Get all production months
     */
    private function getProductionMonths()
    {
        return collect([
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ]);
    }

    /**
     * Get all production farm types
     */
    private function getProductionFarmTypes()
    {
        return collect(['Irrigated', 'Rainfed']);
    }

    /**
     * Delete all crops
     */
    public function deleteAll(Request $request)
    {
        try {
            $totalCount = Crop::count();
            
            if ($totalCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No crops found to delete.'
                ], 400);
            }

            // Delete all crops
            Crop::truncate(); // This is faster than deleting one by one
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted all {$totalCount} crops from the database.",
                'deleted_count' => $totalCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Delete all crops error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting all crops: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all crops on current page
     */
    public function deletePage(Request $request)
    {
        try {
            $request->validate([
                'page' => 'required|integer|min:1',
                'per_page' => 'required|integer|min:1|max:100'
            ]);

            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);

            // Get crops for the current page
            $crops = Crop::paginate($perPage, ['*'], 'page', $page);
            
            if ($crops->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No crops found on page ' . $page . ' to delete.'
                ], 400);
            }

            // Get the IDs of crops on current page
            $cropIds = $crops->pluck('id')->toArray();
            $deletedCount = count($cropIds);
            
            // Delete crops on current page
            Crop::whereIn('id', $cropIds)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} crops from page {$page}.",
                'deleted_count' => $deletedCount,
                'page' => $page
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Delete page crops error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting crops from this page: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available farm types from the database
     */
    private function getFarmTypes()
    {
        return Crop::whereNotNull('farm_type')
                   ->where('farm_type', '!=', '')
                   ->distinct()
                   ->pluck('farm_type')
                   ->sort()
                   ->values();
    }

    /**
     * Get all available years from the database
     */
    private function getAvailableYears()
    {
        return Crop::whereNotNull('year')
                   ->where('year', '>', 0)
                   ->distinct()
                   ->pluck('year')
                   ->sort()
                   ->reverse()
                   ->values();
    }

    /**
     * Get all available statuses from the database
     */
    private function getAvailableStatuses()
    {
        return Crop::whereNotNull('status')
                   ->where('status', '!=', '')
                   ->distinct()
                   ->pluck('status')
                   ->sort()
                   ->values();
    }

    /**
     * Get production ranges for filtering
     */
    private function getProductionRanges()
    {
        $stats = Crop::whereNotNull('production_mt')
                     ->where('production_mt', '>', 0)
                     ->selectRaw('MIN(production_mt) as min, MAX(production_mt) as max, AVG(production_mt) as avg')
                     ->first();

        if (!$stats) {
            return ['min' => 0, 'max' => 100, 'avg' => 50];
        }

        return [
            'min' => floor($stats->min),
            'max' => ceil($stats->max),
            'avg' => round($stats->avg, 2)
        ];
    }

    /**
     * Get area ranges for filtering
     */
    private function getAreaRanges()
    {
        $stats = Crop::whereNotNull('area_planted')
                     ->where('area_planted', '>', 0)
                     ->selectRaw('MIN(area_planted) as min, MAX(area_planted) as max, AVG(area_planted) as avg')
                     ->first();

        if (!$stats) {
            return ['min' => 0, 'max' => 500, 'avg' => 50];
        }

        return [
            'min' => floor($stats->min),
            'max' => ceil($stats->max),
            'avg' => round($stats->avg, 2)
        ];
    }

    /**
     * Get productivity ranges for filtering
     */
    private function getProductivityRanges()
    {
        $stats = Crop::whereNotNull('productivity_mt_ha')
                     ->where('productivity_mt_ha', '>', 0)
                     ->selectRaw('MIN(productivity_mt_ha) as min, MAX(productivity_mt_ha) as max, AVG(productivity_mt_ha) as avg')
                     ->first();

        if (!$stats) {
            return ['min' => 0, 'max' => 50, 'avg' => 15];
        }

        return [
            'min' => floor($stats->min),
            'max' => ceil($stats->max),
            'avg' => round($stats->avg, 2)
        ];
    }
}