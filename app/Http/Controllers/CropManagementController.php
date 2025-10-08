<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CropManagementController extends Controller
{
    /**
     * Display the crop management dashboard
     */
    public function index()
    {
        // Get all crop types from the database
        $cropTypes = DB::table('crops')
            ->select('crop_name')
            ->whereNotNull('crop_name')
            ->where('crop_name', '!=', '')
            ->groupBy('crop_name')
            ->orderBy('crop_name')
            ->get()
            ->pluck('crop_name');

        // Get all municipalities from the database
        $municipalities = DB::table('crops')
            ->select('municipality')
            ->whereNotNull('municipality')
            ->where('municipality', '!=', '')
            ->groupBy('municipality')
            ->orderBy('municipality')
            ->get()
            ->pluck('municipality');

        // Get statistics
        $totalCropTypes = $cropTypes->count();
        $totalMunicipalities = $municipalities->count();
        $totalCrops = DB::table('crops')->count();
        
        // Calculate production and area statistics
        $totalProduction = DB::table('crops')
            ->whereNotNull('production_mt')
            ->where('production_mt', '>', 0)
            ->sum('production_mt');
            
        $totalAreaHarvested = DB::table('crops')
            ->whereNotNull('area_harvested')
            ->where('area_harvested', '>', 0)
            ->sum('area_harvested');
            
        $recentlyAdded = \App\Models\Crop::select('crop_name', 'municipality', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.crop-management.index', compact(
            'cropTypes', 
            'municipalities', 
            'totalCropTypes', 
            'totalMunicipalities', 
            'totalCrops', 
            'totalProduction',
            'totalAreaHarvested',
            'recentlyAdded'
        ));
    }

    /**
     * Store both crop type and municipality
     */
    public function storeCombined(Request $request)
    {
        $request->validate([
            'crop_type_name' => 'required|string|max:255',
            'municipality_name' => 'required|string|max:255',
            'year' => 'nullable|integer|min:2020|max:2030'
        ]);

        try {
            // Create entry with both crop type and municipality
            DB::table('crops')->insert([
                'crop_name' => $request->crop_type_name,
                'name' => $request->crop_type_name,
                'municipality' => $request->municipality_name,
                'farm_type' => 'General Farm',
                'year' => $request->year ?? date('Y'),
                'area_planted' => 0,
                'area_harvested' => 0,
                'production_mt' => 0,
                'productivity_mt_ha' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Crop type and municipality added successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding combined data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding data. Please try again.'
            ]);
        }
    }

    /**
     * Store a new crop type
     */
    public function storeCropType(Request $request)
    {
        $request->validate([
            'crop_type_name' => 'required|string|max:255',
        ]);

        try {
            // Check if crop type already exists
            $exists = DB::table('crops')
                ->where('crop_name', 'LIKE', '%' . $request->crop_type_name . '%')
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Crop type already exists in the database.'
                ]);
            }

            // Create a sample entry with the new crop type
            DB::table('crops')->insert([
                'crop_name' => $request->crop_type_name,
                'name' => $request->crop_type_name,
                'municipality' => 'General',
                'farm_type' => 'Sample Farm',
                'year' => date('Y'),
                'area_planted' => 0,
                'area_harvested' => 0,
                'production_mt' => 0,
                'productivity_mt_ha' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Crop type added successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding crop type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding crop type. Please try again.'
            ]);
        }
    }

    /**
     * Update a crop type
     */
    public function updateCropType(Request $request, $name)
    {
        $request->validate([
            'crop_type_name' => 'required|string|max:255'
        ]);

        try {
            // Decode the URL-encoded name parameter
            $oldCropName = urldecode($name);
            $newCropName = $request->crop_type_name;
            
            // Update all records with the old crop name
            $updated = DB::table('crops')
                ->where('crop_name', $oldCropName)
                ->update([
                    'crop_name' => $newCropName,
                    'name' => $newCropName,
                    'updated_at' => now()
                ]);

            if ($updated > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Crop type updated successfully! {$updated} records affected."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No records found to update.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error updating crop type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating crop type. Please try again.'
            ]);
        }
    }

    /**
     * Delete a crop type
     */
    public function deleteCropType(Request $request)
    {
        $request->validate([
            'crop_name' => 'required|string|max:255'
        ]);

        try {
            $deleted = DB::table('crops')
                ->where('crop_name', $request->crop_name)
                ->delete();

            if ($deleted > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Crop type deleted successfully! {$deleted} records removed."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No records found to delete.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error deleting crop type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting crop type. Please try again.'
            ]);
        }
    }

    /**
     * Store a new municipality
     */
    public function storeMunicipality(Request $request)
    {
        $request->validate([
            'municipality_name' => 'required|string|max:255',
        ]);

        try {
            // Check if municipality already exists
            $exists = DB::table('crops')
                ->where('municipality', 'LIKE', '%' . $request->municipality_name . '%')
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Municipality already exists in the database.'
                ]);
            }

            // Create a sample entry with the new municipality
            DB::table('crops')->insert([
                'crop_name' => 'Rice',
                'name' => 'Rice',
                'municipality' => $request->municipality_name,
                'farm_type' => 'Sample Farm',
                'year' => date('Y'),
                'area_planted' => 0,
                'area_harvested' => 0,
                'production_mt' => 0,
                'productivity_mt_ha' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Municipality added successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding municipality: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding municipality. Please try again.'
            ]);
        }
    }

    /**
     * Update a municipality
     */
    public function updateMunicipality(Request $request)
    {
        $request->validate([
            'old_municipality' => 'required|string|max:255',
            'new_municipality' => 'required|string|max:255',
            'province' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255'
        ]);

        try {
            $updateData = [
                'municipality' => $request->new_municipality,
                'updated_at' => now()
            ];

            if ($request->province) {
                $updateData['province'] = $request->province;
            }

            if ($request->region) {
                $updateData['region'] = $request->region;
            }

            $updated = DB::table('crops')
                ->where('municipality', $request->old_municipality)
                ->update($updateData);

            if ($updated > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Municipality updated successfully! {$updated} records affected."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No records found to update.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error updating municipality: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating municipality. Please try again.'
            ]);
        }
    }

    /**
     * Delete a municipality
     */
    public function deleteMunicipality(Request $request)
    {
        $request->validate([
            'municipality' => 'required|string|max:255'
        ]);

        try {
            $deleted = DB::table('crops')
                ->where('municipality', $request->municipality)
                ->delete();

            if ($deleted > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Municipality deleted successfully! {$deleted} records removed."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No records found to delete.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error deleting municipality: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting municipality. Please try again.'
            ]);
        }
    }
}