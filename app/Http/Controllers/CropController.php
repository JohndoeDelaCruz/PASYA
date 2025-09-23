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
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('crop_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('name', 'LIKE', "%{$searchTerm}%")
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
            $cropFilter = $request->get('crop');
            $query->where(function($q) use ($cropFilter) {
                $q->where('crop_name', $cropFilter)
                  ->orWhere('name', $cropFilter);
            });
        }
        
        // Apply sorting
        if ($request->filled('sort')) {
            $sortDirection = $request->get('sort') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('crop_name', $sortDirection);
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
        
        // Get all unique municipalities and crop names for filters (even if current page has no crops)
        $allMunicipalities = Crop::select('municipality')
            ->whereNotNull('municipality')
            ->where('municipality', '!=', '')
            ->distinct()
            ->orderBy('municipality')
            ->pluck('municipality');
            
        // Also try to get crop names from 'name' field if 'crop_name' is empty
        $cropNamesFromCropName = Crop::select('crop_name')
            ->whereNotNull('crop_name')
            ->where('crop_name', '!=', '')
            ->distinct()
            ->pluck('crop_name');
            
        $cropNamesFromName = Crop::select('name')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->distinct()
            ->pluck('name');
            
        // Combine both sources of crop names
        $allCropNames = $cropNamesFromCropName->merge($cropNamesFromName)->unique()->sort()->values();
        
        // Debug logging
        $totalCrops = Crop::count();
        $sampleCrops = Crop::select(['id', 'municipality', 'crop_name', 'name'])->limit(5)->get();
        
        return view('admin.crops.index', compact('crops', 'allMunicipalities', 'allCropNames'));
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
            
            // Check if this is the new modal format (agricultural statistics data) or old format
            if ($request->has('municipality') || $request->has('crop_name')) {
                // New modal format - agricultural statistics data
                $validated = $request->validate([
                    'municipality' => 'required|string|max:255',
                    'farm_type' => 'required|string|in:irrigated,rainfed,upland,lowland',
                    'year' => 'required|integer|min:2000|max:2030',
                    'crop_name' => 'required|string|max:255',
                    'area_planted' => 'required|numeric|min:0.01|max:99999.99',
                    'area_harvested' => 'required|numeric|min:0.01|max:99999.99',
                    'production' => 'required|numeric|min:0.01|max:99999999.99',
                    'productivity' => 'nullable|numeric|min:0|max:99999.99',
                ]);

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
    public function show(Crop $crop)
    {
        // Load the farmer relationship
        $crop->load('farmer');
        
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crop $crop)
    {
        $crop->delete(); // Soft delete (archive)
        return redirect()->route('admin.crops.index')->with('success', 'Crop archived successfully!');
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
}