<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Cooperative;
use App\Models\Farmer;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.Dashboard');
    }

    public function dataAnalytics()
    {
        return view('admin.DataAnalytics');
    }

    public function cropTrends()
    {
        return view('admin.CropTrendsPatterns');
    }

    public function createAccount(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                // Handle account creation
                $request->validate([
                    'name' => 'required|string|max:255',
                    'username' => 'required|string|max:255|unique:tblFarmers,username',
                    'password' => 'required|string|min:8|confirmed',
                    'municipality' => 'required|string|max:255',
                    'cooperative' => 'nullable|string|max:255',
                    'contact_number' => 'nullable|string|max:20',
                ]);

                // Create farmer
                $farmer = Farmer::create([
                    'farmerName' => $request->name,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'farmerLocation' => $request->municipality,
                    'farmerCooperative' => $request->cooperative,
                    'farmerContactInfo' => $request->contact_number,
                    'is_active' => true,
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Farmer created successfully!',
                        'farmer' => $farmer
                    ]);
                }

                return redirect()->route('admin.create-account')->with('success', 'Farmer created successfully!');
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation error creating farmer: ' . json_encode($e->errors()));
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed: ' . implode(', ', array_map(function($errors) {
                            return implode(', ', $errors);
                        }, $e->errors())),
                        'errors' => $e->errors()
                    ], 422);
                }
                
                return redirect()->back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error('Error creating farmer: ' . $e->getMessage());
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ], 500);
                }
                
                return redirect()->back()->with('error', 'Error creating farmer: ' . $e->getMessage());
            }
        }

        // Get farmers with pagination and filters
        $query = Farmer::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->searchByName($request->search);
        }

        // Apply municipality filter
        if ($request->has('municipality') && $request->municipality) {
            $query->byLocation($request->municipality);
        }

        // Apply cooperative filter
        if ($request->has('cooperative') && $request->cooperative) {
            $query->byCooperative($request->cooperative);
        }

        // Get paginated farmers
        $farmers = $query->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 10))
                        ->appends($request->query());

        // Get unique municipalities for filter dropdown
        $municipalities = Farmer::whereNotNull('farmerLocation')
                               ->distinct()
                               ->pluck('farmerLocation')
                               ->sort()
                               ->values();

        // Get unique cooperatives for filter dropdown
        $cooperatives = Farmer::whereNotNull('farmerCooperative')
                             ->distinct()
                             ->pluck('farmerCooperative')
                             ->sort()
                             ->values();

        return view('admin.CreateAccount', compact('farmers', 'municipalities', 'cooperatives'));
    }

    public function uploadData(Request $request)
    {
        if ($request->isMethod('post')) {
            // Handle file upload
            $request->validate([
                'data_file' => 'required|file|mimes:csv,xlsx,json|max:10240', // 10MB max
                'data_type' => 'required|string',
                'description' => 'nullable|string|max:500',
            ]);

            // Store the file
            $file = $request->file('data_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads/data', $filename, 'public');

            // Here you would typically save file info to database
            // For now, just redirect with success message

            return redirect()->route('admin.upload-data')->with('success', 'Data file uploaded successfully!');
        }

        return view('admin.UploadData');
    }

    public function recommendations()
    {
        return view('admin.Recommendations');
    }

    public function getFarmer($id)
    {
        try {
            $farmer = Farmer::find($id);
            
            if (!$farmer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Farmer not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'farmer' => [
                    'id' => $farmer->farmerID,
                    'name' => $farmer->farmerName,
                    'username' => $farmer->username,
                    'municipality' => $farmer->farmerLocation,
                    'cooperative' => $farmer->farmerCooperative,
                    'contact_number' => $farmer->farmerContactInfo,
                    'is_active' => $farmer->is_active
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching farmer data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching farmer data'
            ], 500);
        }
    }

    public function updateFarmer(Request $request, $id)
    {
        try {
            $farmer = Farmer::find($id);
            
            if (!$farmer) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Farmer not found'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Farmer not found');
            }

            // Validation rules
            $rules = [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:tblFarmers,username,' . $id . ',farmerID',
                'municipality' => 'required|string|max:255',
                'cooperative' => 'nullable|string|max:255',
                'contact_number' => 'nullable|string|max:20',
            ];

            // Only validate password if it's provided
            if ($request->filled('password')) {
                $rules['password'] = 'required|string|min:8|confirmed';
            }

            $request->validate($rules);

            // Update farmer data
            $updateData = [
                'farmerName' => $request->name,
                'username' => $request->username,
                'farmerLocation' => $request->municipality,
                'farmerCooperative' => $request->cooperative,
                'farmerContactInfo' => $request->contact_number,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $farmer->update($updateData);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Farmer updated successfully!',
                    'farmer' => $farmer
                ]);
            }

            return redirect()->route('admin.create-account')->with('success', 'Farmer updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error updating farmer: ' . json_encode($e->errors()));
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', array_map(function($errors) {
                        return implode(', ', $errors);
                    }, $e->errors())),
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating farmer: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error updating farmer: ' . $e->getMessage());
        }
    }

    public function toggleFarmerStatus(Request $request, $id)
    {
        $farmer = Farmer::findOrFail($id);
        
        $farmer->update([
            'is_active' => $request->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Farmer status updated successfully!'
        ]);
    }

    public function deleteFarmer($id)
    {
        $farmer = Farmer::findOrFail($id);
        $farmer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Farmer deleted successfully!'
        ]);
    }

    public function batchImportFarmers(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));
        
        // Remove header row
        $header = array_shift($data);
        
        $imported = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Assuming CSV format: farmerName, username, farmerLocation, farmerCooperative, farmerContactInfo
                if (count($row) >= 3) {
                    Farmer::create([
                        'farmerName' => $row[0] ?? '',
                        'username' => $row[1] ?? 'farmer_' . time() . '_' . $index,
                        'password' => Hash::make($row[2] ?? 'password123'), // Default or provided password
                        'farmerLocation' => $row[3] ?? '',
                        'farmerCooperative' => $row[4] ?? null,
                        'farmerContactInfo' => $row[5] ?? null,
                        'is_active' => true,
                    ]);

                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'count' => $imported,
            'errors' => $errors,
            'message' => "Successfully imported {$imported} farmers."
        ]);
    }
}