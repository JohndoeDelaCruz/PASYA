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
    public function index()
    {
        // Admin can see all crops from all farmers
        $crops = Crop::with('farmer')->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.crops.index', compact('crops'));
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

        Crop::create($validated);

        return redirect()->route('admin.crops.index')->with('success', 'Crop added successfully!');
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
            'actual_harvest_date' => 'nullable|date|after_or_equal:planting_date',
            'area_hectares' => 'required|numeric|min:0.01|max:9999.99',
            'description' => 'nullable|string|max:1000',
            'status' => ['required', Rule::in(['planted', 'growing', 'harvested', 'failed'])],
            'expected_yield_kg' => 'nullable|numeric|min:0|max:99999999.99',
            'actual_yield_kg' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        $crop->update($validated);

        return redirect()->route('admin.crops.index')->with('success', 'Crop updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crop $crop)
    {
        $crop->delete();

        return redirect()->route('admin.crops.index')->with('success', 'Crop deleted successfully!');
    }

    /**
     * Show the import/export page
     */
    public function importExport()
    {
        return view('admin.crops.import-export');
    }

    /**
     * Export crops to CSV
     */
    public function export(Request $request, CropImportExportService $service)
    {
        $crops = null;
        
        // If specific farmer is selected, filter crops
        if ($request->has('farmer_id') && $request->farmer_id) {
            $crops = Crop::with('farmer')->where('farmer_id', $request->farmer_id)->get();
        }

        $csv = $service->exportToCsv($crops);
        $filename = 'crops_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import crops from CSV
     */
    public function import(Request $request, CropImportExportService $service)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $results = $service->importFromCsv($request->file('csv_file'));

        $message = "Import completed: {$results['success']} successful, {$results['errors']} errors.";
        
        if (!empty($results['messages'])) {
            $message .= "\n\nDetails:\n" . implode("\n", $results['messages']);
        }

        if ($results['errors'] > 0) {
            return redirect()->route('admin.crops.import-export')
                ->with('warning', $message);
        }

        return redirect()->route('admin.crops.import-export')
            ->with('success', $message);
    }

    /**
     * Download CSV template for import
     */
    public function downloadTemplate(CropImportExportService $service)
    {
        $csv = $service->getCsvTemplate();
        $filename = 'crops_import_template.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export individual crop
     */
    public function exportSingle(Crop $crop, CropImportExportService $service)
    {
        $crops = collect([$crop->load('farmer')]);
        $csv = $service->exportToCsv($crops);
        $filename = 'crop_' . $crop->id . '_' . str_replace(' ', '_', $crop->name) . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
